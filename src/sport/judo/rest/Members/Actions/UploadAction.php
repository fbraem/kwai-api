<?php

namespace Judo\REST\Members\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use League\Csv\Reader;

use Cake\Datasource\ConnectionManager;

use Judo\Domain\Member\MembersTable;
use Judo\Domain\Member\MemberTransformer;

use Domain\Person\CountriesTable;
use Domain\Person\ContactsTable;
use Domain\Person\PersonsTable;

class UploadAction
{
    private $countries = [
        'Belgie' => 'BEL',
        'Thailand' => 'THA',
        'Nederland' => 'NLD'
    ];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $connection = ConnectionManager::get('default');
        $connection->begin();

        $countriesTable = CountriesTable::getTableFromRegistry();
        $countriesTable->find()->where(['iso_3' => array_values($this->countries)]);
        $result = $countriesTable->find()->all();
        foreach ($result as $country) {
            $countryName = array_search($country->iso_3, $this->countries);
            if ($countryName !== false) {
                $this->countries[$countryName] = $country;
            }
        }

        $files = $request->getUploadedFiles();
        if (!isset($files['csv'])) {
            return $response->withStatus(400);
        }
        $uploadedFilename = $files['csv']->getClientFilename();

        $reader = Reader::createFromPath($files['csv']->getStream()->getMetadata('uri'));
        $reader->setDelimiter(';');
        $reader->setHeaderOffset(0);

        $members = [];
        $records = $reader->getRecords();

        $membersTable = MembersTable::getTableFromRegistry();
        $contactsTable = ContactsTable::getTableFromRegistry();
        $personsTable = PersonsTable::getTableFromRegistry();

        $query = $membersTable->find();
        $lastImportId = $query->select(['last_import_id' => $query->func()->max('import_id')])->first()->last_import_id ?? 0;
        $newImportId = $lastImportId + 1;

        $members = $membersTable
            ->find()
            ->contain(['Person', 'Person.Contact', 'Person.Nationality'])
            ->indexBy('license')
            ->toArray();

        foreach ($records as $offset => $record) {
            $member = $members[$record['Vergunning']];
            if (!$member) {
                $member = $membersTable->newEntity();
                $members[$record['Vergunning']] = $member;
            }

            $city = null;
            $postal_code = null;
            if (strstr($record['Gemeente'], PHP_EOL)) {
                // A country in the second line
                $parts = explode(PHP_EOL, $record['Gemeente']);
                $city = $parts[0];
                $country = $this->countries[$parts[1]] ?? null;
                // At the moment only support for Nederland
                if ($parts[1] == 'Nederland') {
                    $parts = explode(' ', $city, 3);
                    if (count($parts) == 2) {
                        $postal_code = $parts[0];
                        $city = $parts[1];
                    } else {
                        $postal_code = $parts[0] . ' ' . $parts[1];
                        $city = $parts[2];
                    }
                }
            } else {
                $country = $this->countries['Belgie'];
                $parts = explode(' ', $record['Gemeente'], 2);
                $postal_code = $parts[0];
                $city = $parts[1];
            }

            if ($member) {
                $person = $member->person;
                if (! $person) {
                    $person = $personsTable->newEntity();
                    $member->person = $person;
                }
            } else {
                $person = $personsTable->newEntity();
                $member->person = $person;
            }

            if (!$person->contact) {
                $contact = $contactsTable->newEntity();
                $person->contact = $contact;
            } else {
                $contact = $person->contact;
            }

            $contact->email = $record['Email'];
            $contact->tel = $record['Tel'];
            $contact->mobile = $record['Gsm'];
            $contact->address = $record['Straat'];
            $contact->postal_code = $postal_code;
            $contact->city = $city;
            $contact->county = null;
            $contact->country = $country;
            $contact->remark = 'Imported';
            $contactsTable->save($contact);

            $person->firstname = utf8_encode($record['Voornaam']);
            $person->lastname = utf8_encode($record['Naam']);
            $person->birthdate = \Carbon\Carbon::createFromFormat(
                'd/m/Y',
                $record['Geb Datum']
            );
            $person->gender = $record['Geslacht'] == 'M' ? 1 : 2;
            $person->nationality = $this->countries[$record['Nation.']] ?? null;
            $personsTable->save($person);

            if (!$member) {
                $member = $membersTable->newEntity();
                $member->competition = false;
            }
            $member->license = $record['Vergunning'];
            $member->license_end_date = \Carbon\Carbon::createFromFormat(
                'd/m/Y',
                $record['Geldig Tot']
            );
            $member->import_id = $newImportId;

            $membersTable->save($member);
        }

        $connection->commit();

        return (new \Core\ResourceResponse(
            MemberTransformer::createForCollection($members)
        ))($response);
    }
}
