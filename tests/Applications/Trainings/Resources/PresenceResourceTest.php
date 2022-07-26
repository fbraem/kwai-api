<?php
declare(strict_types=1);

use Kwai\Applications\Trainings\Resources\PresenceResource;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Date;
use Kwai\Core\Domain\ValueObjects\Gender;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\Member;
use Kwai\Modules\Trainings\Domain\MemberEntity;
use Kwai\Modules\Trainings\Domain\ValueObjects\Presence;

it('can serialize a presence as JSON:API resource', function () {
   $presence = new Presence(
       member: new MemberEntity(
           1,
           new Member(
               license: '12345',
               licenseEndDate: Date::createFromString('2022-12-01'),
               name: new Name('Jigoro', 'Kano'),
               gender: Gender::MALE,
               birthDate: Date::createFromString('1860-10-28')
           )
       ),
       creator: new Creator(
           1,
           new Name('Jigoro', 'Kano')
       )
   );

   $resource = new PresenceResource($presence);

    try {
        $jsonapi = JSONAPI\Document::createFromObject($resource)->serialize();
    } catch (JSONAPI\Exception $e) {
        $this->fail((string) $e);
    }

    $json = json_decode($jsonapi);

    expect($json)
        ->toHaveProperty('data')
        ->and($json->data)
            ->toMatchObject([
                'type' => 'presences',
                'id' => '1'
            ])
            ->toHaveProperty('attributes')
        ->and($json->data->attributes)
            ->toMatchObject([
                'remark' => null,
                'name' => 'Jigoro Kano',
                'gender' => 1,
                'birthdate' => '1860-10-28',
                'license' => '12345',
                'license_end_date' => '2022-12-01'
            ])
    ;
});
