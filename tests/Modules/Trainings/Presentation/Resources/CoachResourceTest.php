<?php
declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\Modules\Trainings\Presentation\Resources\CoachResource;
use Kwai\JSONAPI;

it('can serialize a coach entity to JSON:API', function () {
    $coach = new Entity(
        1,
        new Coach(
            new Name('Jigoro', 'Kano')
        )
    );

    $resource = new CoachResource($coach);

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
            'type' => 'coaches',
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
