<?php

namespace Judo\REST\Members\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;
use League\Csv\Reader;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

use League\Fractal;

class UploadAction implements \Core\ActionInterface
{
    private $countries = [
        'Belgie' => 'BEL',
        'Thailand' => 'THA',
        'Nederland' => 'NLD'
    ];

    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $db = $request->getAttribute('clubman.container')['db'];
        $db->getDriver()->getConnection()->beginTransaction();

        $countriesTable = new \Domain\Person\CountriesTable($db);
        $countriesTable->whereIso3(array_values($this->countries));
        $result = $countriesTable->find();
        foreach ($result as $country) {
            $countryName = array_search($country->iso_3(), $this->countries);
            if ($countryName !== false) {
                $this->countries[$countryName] = $country;
            }
        }

        $files = $request->getUploadedFiles();
        if (!isset($files['csv'])) {
            return (new HTTPCodeResponder(new Responder(), 400))->respond();
        }
        $uploadedFilename = $files['csv']->getClientFilename();

        $reader = Reader::createFromPath($files['csv']->getStream()->getMetadata('uri'));
        $reader->setDelimiter(';');
        $reader->setHeaderOffset(0);

        $members = [];
        $records = $reader->getRecords();

        foreach ($records as $offset => $record) {
            try {
                $member = (new \Judo\Domain\Member\MembersTable($db))->whereLicense($record['Vergunning'])->findOne();
                continue;
            } catch (\Domain\NotFoundException $nfe) {
                // Ignore
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

            $contact = new \Domain\Person\Contact($db, [
                'email' => $record['Email'],
                'tel' => $record['Tel'],
                'mobile' => $record['Gsm'],
                'address' => $record['Straat'],
                'postal_code' => $postal_code,
                'city' => $city,
                'county' => null,
                'country' => $country,
                'remark' => 'Imported'
            ]);

            try {
                $contact->store();
            } catch (\Exception $e) {
                echo var_dump($record);
                exit;
            }

            $person = new \Domain\Person\Person($db, [
                'firstname' => utf8_encode($record['Voornaam']),
                'lastname' => utf8_encode($record['Naam']),
                'birthdate' => \Carbon\Carbon::createFromFormat('d/m/Y', $record['Geb Datum'])->format('Y-m-d'),
                'gender' => $record['Geslacht'] == 'M' ? 1 : 2,
                'nationality' => $this->countries[$record['Nation.']] ?? null,
                'contact' => $contact
            ]);
            $person->store();

            $member = new \Judo\Domain\Member\Member($db, [
                'license' => $record['Vergunning'],
                'license_end_date' => \Carbon\Carbon::createFromFormat('d/m/Y', $record['Geldig Tot'])->format('Y-m-d'),
                'competition' => false,
                'person' => $person
            ]);
            $member->store();

            $members[] = $member;
        }

        $db->getDriver()->getConnection()->commit();

        $payload->setOutput(new Fractal\Resource\Collection($members, new \Judo\Domain\Member\MemberTransformer(), 'sport_judo_members'));

        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}
