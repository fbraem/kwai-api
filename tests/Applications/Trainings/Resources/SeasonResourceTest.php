<?php
declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\Season;
use Kwai\Modules\Trainings\Presentation\Resources\SeasonResource;

it('can serialize a season to a JSONAPI resource', function () {
    $season = new Entity(
        1,
        new Season(name: '2021-2022')
    );

    $resource = new SeasonResource($season);

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
            'type' => 'seasons',
            'id' => '1'
        ])
        ->toHaveProperty('attributes')
    ;
    expect($json->data->attributes)
        ->toMatchObject([
            'name' => '2021-2022'
        ])
    ;
});
