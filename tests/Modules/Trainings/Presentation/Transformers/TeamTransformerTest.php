<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Trainings\Domain\Team;
use Kwai\Modules\Trainings\Presentation\Transformers\TeamTransformer;
use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;

it('can transform a team', function () {
    $team = new Entity(
        1,
        new Team(
            name: 'Competitors'
        )
    );
    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer());
    $data = $fractal
        ->createData(TeamTransformer::createForItem(
            $team
        ))
        ->toArray()
    ;
    expect($data)
        ->toBeArray()
        ->and($data['data'])
        ->toBeArray()
        ->toMatchArray([
            'id' => '1',
            'name' => 'Competitors'
        ])
    ;
}) ;
