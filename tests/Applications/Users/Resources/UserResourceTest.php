<?php
declare(strict_types=1);

use Kwai\Applications\Users\Resources\ResourceTypes;
use Kwai\Applications\Users\Resources\UserResource;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserEntity;

it('can serialize a User into a JSON:API resource', function () {
    $uuid = new UniqueId();
    $user = new UserEntity(
        1,
        new User(
            uuid: $uuid,
            emailAddress: new EmailAddress('jigoro.kano@kwai.com'),
            username: new Name('Jigoro', 'Kano')
        )
    );

    $resource = new UserResource($user);

    try {
        $jsonapi = JSONAPI\Document::createFromObject($resource)->serialize();
    } catch (JSONAPI\Exception $e) {
        $this->fail((string) $e);
    }

    $json = json_decode($jsonapi);

    expect($json)
        ->toHaveProperty('data')
    ;
    expect($json->data)
        ->toMatchObject([
            'type' => ResourceTypes::USERS,
            'id' => (string) $uuid
        ])
        ->toHaveProperty('attributes')
    ;
    expect($json->data->attributes)
        ->toMatchObject([
            'email' => 'jigoro.kano@kwai.com',
            'username' => 'Jigoro Kano',
            'first_name' => 'Jigoro',
            'last_name' => 'Kano'
        ])
    ;
});
