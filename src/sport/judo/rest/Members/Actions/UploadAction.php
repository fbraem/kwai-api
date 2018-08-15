<?php

namespace Judo\REST\Members\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;
use League\Csv\Reader;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

class UploadAction implements \Core\ActionInterface
{
    private $countries = [
        'Belgie' => 'BEL',
        'Thailand' => 'THA',
        'Nederland' => 'NLD'
    ];

    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $connection = \Cake\Datasource\ConnectionManager::get('default');
        $connection->begin();

        $countriesTable = \Domain\Person\CountriesTable::getTableFromRegistry();
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
            return (
                new HTTPCodeResponder(
                    new Responder(),
                    400
                )
            )->respond();
        }
        $uploadedFilename = $files['csv']->getClientFilename();

        $reader = Reader::createFromPath($files['csv']->getStream()->getMetadata('uri'));
        $reader->setDelimiter(';');
        $reader->setHeaderOffset(0);

        $members = [];
        $records = $reader->getRecords();

        $membersTable = \Judo\Domain\Member\MembersTable::getTableFromRegistry();

        foreach ($records as $offset => $record) {
            $member = $membersTable
                ->find()
                ->where(['license' => $record['Vergunning']])
                ->first();
            if ($member) {
                continue;
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

            $contactsTable = \Domain\Person\ContactsTable::getTableFromRegistry();
            $contact = $contactsTable->newEntity();
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

            $personsTable = \Domain\Person\PersonsTable::getTableFromRegistry();
            $person = $personsTable->newEntity();
            $person->firstname = utf8_encode($record['Voornaam']);
            $person->lastname = utf8_encode($record['Naam']);
            $person->birthdate = \Carbon\Carbon::createFromFormat(
                'd/m/Y',
                $record['Geb Datum']
            )->format('Y-m-d');
            $person->gender = $record['Geslacht'] == 'M' ? 1 : 2;
            $person->nationality = $this->countries[$record['Nation.']] ?? null;
            $person->contact = $contact;
            $personsTable->save($person);

            $membersTable = \Judo\Domain\Member\MembersTable::getTableFromRegistry();
            $member = $membersTable->newEntity();
            $member->license = $record['Vergunning'];
            $member->license_end_date = \Carbon\Carbon::createFromFormat(
                'd/m/Y',
                $record['Geldig Tot']
            )->format('Y-m-d');
            $member->competition = false;
            $member->person = $person;
            $membersTable->save($member);

            $members[] = $member;
        }

        $connection->commit();

        $payload->setOutput(\Judo\Domain\Member\MemberTransformer::createForCollection($members));

        return (
            new JSONResponder(
                new Responder(),
                $payload
            )
        )->respond();
    }
}
