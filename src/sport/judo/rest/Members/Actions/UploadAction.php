<?php
/** @noinspection SpellCheckingInspection */

namespace Judo\REST\Members\Actions;

use Carbon\Carbon;
use League\Csv\Statement;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use League\Csv\Reader;

use Judo\Domain\Member\MembersTable;
use Judo\Domain\Member\MemberTransformer;
use Domain\Member\MemberImportsTable;

use Domain\Person\CountriesTable;
use Domain\Person\ContactsTable;
use Domain\Person\PersonsTable;

use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;

class UploadAction
{
    const VERGUNNING = 0;
    const VOORNAAM = 1;
    const ACHTERNAAM = 2;
    const GESLACHT = 3;
    const GEBOORTEDATUM = 4;
    const STRAATNUMMER = 5;
    const POSTNUMMER = 6;
    const GEMEENTE = 7;
    const LAND = 8;
    const NATIONALITEIT = 9;
    const TEL_1 = 10;
    const TEL_2 = 11;
    const EMAIL = 12;
    const VERVALDAG = 16;
    const GRAAD = 33;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $countriesTable = CountriesTable::getTableFromRegistry();
        $countries = $countriesTable->find()->all();

        $files = $request->getUploadedFiles();
        if (!isset($files['csv'])) {
            return $response->withStatus(400);
        }

        $reader = Reader::createFromPath($files['csv']->getStream()->getMetadata('uri'));
        $reader->setDelimiter("\t");

        // Skip the first line, because that's a header we can't use...
        $stmt = (new Statement())->offset(1);
        $records = $stmt->process($reader);

        $membersTable = MembersTable::getTableFromRegistry();
        $contactsTable = ContactsTable::getTableFromRegistry();
        $personsTable = PersonsTable::getTableFromRegistry();

        $memberImportsTable = MemberImportsTable::getTableFromRegistry();
        $memberImport = $memberImportsTable->newEntity();
        $memberImport->filename = $files['csv']->getClientFilename();
        $memberImport->user_id = $request->getAttribute('kwai.user')->id();
        $memberImportsTable->save($memberImport);

        $members = $membersTable
            ->find()
            ->contain(['Person', 'Person.Contact', 'Person.Nationality'])
            ->indexBy('license')
            ->toArray();

        foreach ($records as $offset => $record) {
            $member = $members[$record[self::VERGUNNING]] ?? null;
            if (!$member) {
                $member = $membersTable->newEntity();
                $members[$record[self::VERGUNNING]] = $member;
            }

            $city = $record[self::GEMEENTE];
            $postal_code = $record[self::POSTNUMMER];
            $country = $countries->firstMatch(['iso_2' => $record[self::LAND]]);

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

            $contact->email = $record[self::EMAIL];
            $contact->tel = $record[self::TEL_1];
            $contact->mobile = $record[self::TEL_2];
            $contact->address = $record[self::STRAATNUMMER];
            $contact->postal_code = $postal_code;
            $contact->city = $city;
            $contact->county = null;
            $contact->country = $country;
            $contact->remark = 'Imported';
            $contactsTable->save($contact);

            $person->firstname = utf8_encode($record[self::VOORNAAM]);
            $person->lastname = utf8_encode($record[self::ACHTERNAAM]);
            $person->birthdate = Carbon::createFromFormat(
                'Y-m-d',
                $record[self::GEBOORTEDATUM]
            );
            $person->gender = $record[self::GESLACHT] == 'M' ? 1 : 2;
            $person->nationality = $countries->firstMatch(['iso_2' => $record[self::NATIONALITEIT]]);
            $personsTable->save($person);

            if (!$member) {
                $member = $membersTable->newEntity();
                $member->competition = false;
            }
            $member->license = $record[self::VERGUNNING];
            $member->license_end_date = Carbon::createFromFormat(
                'Y-m-d',
                $record[self::VERVALDAG]
            );
            $member->import_id = $memberImport->id;

            $membersTable->save($member);
        }

        $membersTable->updateAll(
            [
                'active' => false
            ],
            [
                'OR' => [
                    'import_id !=' => $memberImport->id,
                    'import_id IS NULL'
                ]
            ]
        );

        return (new ResourceResponse(
            MemberTransformer::createForCollection($members)
        ))($response);
    }
}
