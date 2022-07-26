<?php
declare(strict_types=1);

use Kwai\Applications\Trainings\Resources\CoachResource;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\CoachEntity;

it('can serialize a coach entity to JSON:API', function () {
    $coach = new CoachEntity(
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
        ->and($json->data)
            ->toMatchObject([
                'type' => 'coaches',
                'id' => '1'
            ])
            ->toHaveProperty('attributes')
        ->and($json->data->attributes)
            ->toMatchObject([
                'name' => 'Jigoro Kano'
            ])
    ;
});
