<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Trainings\Domain\Season;
use Kwai\Modules\Trainings\Presentation\Transformers\SeasonTransformer;
use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;

it('can transform a season', function () {
    $season = new Entity(
        1,
        new Season(
            name: '2020-2021'
        )
    );
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer());
    $data = $fractal
        ->createData(SeasonTransformer::createForItem(
            $season
        ))
        ->toArray()
    ;
    expect($data)
        ->toBeArray()
        ->and($data['data'])
        ->toBeArray()
        ->toMatchArray([
            'id' => '1',
            'name' => '2020-2021'
        ])
    ;
}) ;
