<?php
declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\JSONAPI;
use Kwai\Modules\Coaches\Domain\Member;
use Kwai\Modules\Coaches\Presentation\Resources\MemberResource;

it('can serialize a member resource', function () {
    $member = new Entity(
        1,
        new Member(
            name: new Name('Jigoro', 'Kano')
        )
    );

    $resource = new MemberResource($member);

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
            'type' => 'members',
            'id' => '1'
        ])
        ->toHaveProperty('attributes')
    ;
    expect($json->data->attributes)
        ->toMatchObject([
            'name' => 'Jigoro Kano'
        ])
    ;
});
