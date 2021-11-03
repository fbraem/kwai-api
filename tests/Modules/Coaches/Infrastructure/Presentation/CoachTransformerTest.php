<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\Coaches\Domain\Coach;
use Kwai\Modules\Coaches\Domain\Member;
use Kwai\Modules\Coaches\Presentation\Transformers\CoachTransformer;
use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;

it('can transform a coach', function ($coach) {
    $fractal = new Manager();
    $fractal->setSerializer(new JsonApiSerializer());
    $data = $fractal
        ->createData(CoachTransformer::createForItem($coach))
        ->toArray()
    ;

    expect($data)
        ->toHaveKey('data.type', 'coaches')
        ->toHaveKey('data.id', '1')
        ->toHaveKey('data.attributes.updated_at', null)
        ->toHaveKey('data.attributes.bio', 'The founder of Judo')
        ->toHaveKey('data.attributes.diploma', 'Founder')
    ;
})
->with([
    new Entity(
        1,
        new Coach(
            bio: 'The founder of Judo',
            member: new Entity(1, new Member(
                name: new Name('Jigoro', 'Kano')
            )),
            active: true,
            diploma: 'Founder'
        )
    )
]);
