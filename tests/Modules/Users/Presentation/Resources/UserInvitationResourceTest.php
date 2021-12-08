<?php
declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\UserInvitation;
use Kwai\Modules\Users\Presentation\Resources\UserInvitationResource;

it('can serialize an UserInvitation to a JSON:API resource', function () {
    $uuid = new UniqueId();
    $account = new Entity(
        1,
        new UserInvitation(
            emailAddress: new EmailAddress('ingrid.berghmans@kwai.com'),
            expiration: Timestamp::createNow(),
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
            'type' => 'user_invitations',
            'id' => '1'
        ])
        ->toHaveProperty('attributes')
    ;
    expect($json->data->attributes)
        ->toMatchObject([
            'email' => 'ingrid.berghmans@kwai.com',
            'username' => 'Ingrid Berghmans',
            'uuid' => (string) $uuid,
            'confirmed_at' => null
        ])
    ;
});
