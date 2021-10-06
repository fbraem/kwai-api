<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Club\Domain\Team;
use Kwai\Modules\Club\Presentation\Transformers\TeamTransformer;
use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;

it('can transform a team', function ($team) {
    $fractal = new Manager();
    $fractal->setSerializer(new JsonApiSerializer());
    $resource = TeamTransformer::createForItem($team);
    $data = $fractal->createData($resource)->toArray();
    expect($data)
        ->toHaveKey('data.type', 'teams')
        ->toHaveKey('data.attributes.name', 'U13')
    ;
})->with([
    new Entity(
        1,
        new Team(
            'U13'
        )
    )
]);
