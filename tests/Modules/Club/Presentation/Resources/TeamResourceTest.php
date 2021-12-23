<?php
declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\JSONAPI;
use Kwai\Modules\Club\Domain\Team;
use Kwai\Modules\Club\Presentation\Resources\TeamResource;

it('can serialize a team resource', function () {
    $team = new Entity(
        1,
        new Team('Cadets')
    );

    $resource = new TeamResource($team);

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
            'type' => 'teams',
            'id' => '1'
        ])
        ->toHaveProperty('attributes')
    ;
    expect($json->data->attributes)
        ->toMatchObject([
            'name' => 'Cadets'
        ])
    ;
});
