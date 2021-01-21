<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\Coaches\Domain\Coach;
use Kwai\Modules\Coaches\Domain\Member;
use Kwai\Modules\Coaches\Presentation\Transformers\CoachTransformer;
use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;

it('can transform a coach', function () {
    $coach = new Coach(
        bio: 'The founder of Judo',
        member: new Entity(1, new Member(
            name: new Name('Jigoro', 'Kano')
        )),
        active: true,
        creator: new Creator(
            1,
            new Name()
        ),
        diploma: 'Founder'
    );

    $fractal = new Manager();
    $fractal->setSerializer(new DataArraySerializer());
    $data = $fractal
        ->createData(CoachTransformer::createForItem(new Entity(1, $coach)))
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
            'bio' => 'The founder of Judo'
        ])
    ;
});
