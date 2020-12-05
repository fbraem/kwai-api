<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Time;
use Kwai\Core\Domain\ValueObjects\Weekday;
use Kwai\Modules\Trainings\Domain\Definition;
use Kwai\Modules\Trainings\Presentation\Transformers\DefinitionTransformer;
use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;

it('can transform a definition', function () {
    $definition = new Definition(
        name: 'Competition Training',
        description: 'Training for all competitors',
        creator: new Creator(
            1, new Name('Jigoro', 'Kano')
        ),
        weekday: Weekday::WEDNESDAY(),
        startTime: new Time(20, 0, 'Europe/Brussels'),
        endTime: new Time(21, 0, 'Europe Brussels')
    );
    $entity = new Entity(1, $definition);
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer());
    $data = $fractal
        ->createData(DefinitionTransformer::createForItem(
            $entity,
        ))
        ->toArray()
    ;
    expect($data)
        ->toBeArray()
        ->toHaveKey('data')
        ->and($data['data'])
        ->toBeArray()
        ->toMatchArray([
            'id' => '1',
            'updated_at' => null,
            'start_time' => '20:00',
            'end_time' => '21:00',
            'time_zone' => 'Europe/Brussels',
            'location' => null,
            'active' => true
        ])
    ;
});
