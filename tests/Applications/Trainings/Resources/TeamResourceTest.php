<?php
declare(strict_types=1);

use Kwai\Applications\Trainings\Resources\TeamResource;
use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\Team;
use Kwai\Modules\Trainings\Domain\TeamEntity;

it('can serialize a team to a JSONAPI resource', function () {
    $team = new TeamEntity(
        1,
        new Team(name: 'U13')
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
        ->and($json->data)
            ->toMatchObject([
                'type' => 'teams',
                'id' => '1'
            ])
            ->toHaveProperty('attributes')
        ->and($json->data->attributes)
            ->toMatchObject([
                'name' => 'U13'
            ])
    ;
});
