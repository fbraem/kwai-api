<?php
declare(strict_types=1);

use Kwai\Applications\Users\Resources\ResourceTypes;
use Kwai\Applications\Users\Resources\UserAccountResource;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\UserAccountEntity;
use Kwai\Modules\Users\Domain\ValueObjects\Password;

it('can serialize an UserAccount to a JSON:API resource', function () {
    $uuid = new UniqueId();
    $account = new UserAccountEntity(
        1,
        new UserAccount(
            user: new User(
                uuid: $uuid,
                emailAddress: new EmailAddress('jigoro.kano@kwai.com'),
                username: new Name('Jigoro', 'Kano')
            ),
            password: Password::fromString('Test')
        )
    );

    $resource = new UserAccountResource($account);

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
            'type' => ResourceTypes::USER_ACCOUNTS,
            'id' => (string) $uuid
        ])
        ->toHaveProperty('attributes')
    ;
    expect($json->data->attributes)
        ->toMatchObject([
            'email' => 'jigoro.kano@kwai.com',
            'username' => 'Jigoro Kano'
        ])
    ;
});
