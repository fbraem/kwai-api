<?php
declare(strict_types=1);

use Kwai\Applications\Users\Resources\ResourceTypes;
use Kwai\Applications\Users\Resources\UserInvitationResource;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\LocalTimestamp;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\UserInvitation;
use Kwai\Modules\Users\Domain\UserInvitationEntity;

it('can serialize an UserInvitation to a JSON:API resource', function () {
    $uuid = new UniqueId();
    $account = new UserInvitationEntity(
        1,
        new UserInvitation(
            emailAddress: new EmailAddress('ingrid.berghmans@kwai.com'),
            expiration: new LocalTimestamp(Timestamp::createNow(), 'UTC'),
            name: 'Ingrid Berghmans',
            creator: new Creator(1, new Name('Jigoro', 'Kano')),
            uuid: $uuid,
        )
    );

    $resource = new UserInvitationResource($account);

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
            'type' => ResourceTypes::USER_INVITATIONS,
            'id' => (string) $uuid
        ])
        ->toHaveProperty('attributes')
    ;
    expect($json->data->attributes)
        ->toMatchObject([
            'email' => 'ingrid.berghmans@kwai.com',
            'name' => 'Ingrid Berghmans',
            'confirmed_at' => null
        ])
    ;
});
