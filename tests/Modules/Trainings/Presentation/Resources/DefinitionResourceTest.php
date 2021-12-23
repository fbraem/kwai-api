<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Location;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Time;
use Kwai\Core\Domain\ValueObjects\TimePeriod;
use Kwai\Core\Domain\ValueObjects\Weekday;
use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\Definition;
use Kwai\Modules\Trainings\Presentation\Resources\DefinitionResource;

it('can serialize a definition entity to JSON:API', function () {
    $definition = new Entity(
        1,
        new Definition(
            name: 'Monday',
            description: 'Training on monday',
            weekday: Weekday::MONDAY(),
            // location: new Location('Sporthal Stekene'),
            period: new TimePeriod(
                new Time(19, 0, 'Europe/Brussels'),
                new Time(21, 0, 'Europe/Brussels')
            ),
            creator: new Creator(
                1,
                new Name('Jigoro', 'Kano')
            )
        )
    );

    $resource = new DefinitionResource($definition);

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
            'type' => 'definitions',
            'id' => '1'
        ])
        ->toHaveProperty('attributes')
    ;
    expect($json->data->attributes)
        ->toMatchObject([
            'active' => true,
            'weekday' => 1,
            'start_time' => '19:00',
            'end_time' => '21:00',
            'time_zone' => 'Europe/Brussels',
            'location' => null
        ])
    ;
});
