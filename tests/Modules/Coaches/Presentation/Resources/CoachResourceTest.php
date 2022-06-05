<?php
declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Modules\Coaches\Domain\Coach;
use Kwai\Modules\Coaches\Domain\Member;
use Kwai\Modules\Coaches\Presentation\Resources\CoachResource;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserEntity;

it('can serialize a coach resource', function () {
    $coach = new Entity(
        1,
        new Coach(
            member: new Entity(
                1,
                new Member(
                    name: new Name('Jigoro', 'Kano')
                )
            ),
            active: false
        )
    );

    $resource = new CoachResource(
        $coach,
        new UserEntity(
            1,
            new User(
                new UniqueId(),
                new EmailAddress('jigoro.kano@kwai.com'),
                new Name(
                    'Jigoro',
                    'Kano'
                )
            )
        )
    );

    try {
        $jsonapi = JSONAPI\Document::createFromObject($resource)->serialize();
    } catch (JSONAPI\Exception $e) {
        $this->fail((string) $e);
    }

    $json = json_decode($jsonapi);

    expect($json)
        ->toHaveProperty('data')
        ->toHaveProperty('included')
    ;
    expect($json->data)
        ->toMatchObject([
            'type' => 'coaches',
            'id' => '1'
        ])
        ->toHaveProperty('attributes')
    ;
    expect($json->data->attributes)
        ->toMatchObject([
            'active' => false
        ])
    ;
    expect($json->included)
        ->toBeArray()
        ->and($json->included[0])
        ->toHaveProperty('type', 'members')
        ->toHaveProperty('id', '1')
    ;
});
