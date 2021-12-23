<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Coaches\Domain\Coach;
use Kwai\Modules\Coaches\Domain\Member;
use Kwai\Modules\Coaches\Infrastructure\Mappers\CoachMapper;

it('can map data to a coach', function () {
    $data = collect([
       'member' => collect([
           'id' => '1',
            'firstname' => 'Jigoro',
            'lastname' => 'Kano'
       ]),
       'creator' => collect([
           'id' => '1'
       ]),
       'description' => 'Our coach',
       'diploma' => 'Founder',
       'active' => 0,
       'remark' => 'The founder of Judo'
    ]);
    $coach = CoachMapper::toDomain($data);
    expect($coach)
       ->toBeInstanceOf(Coach::class)
       ->and($coach->getMember())
       ->toBeInstanceOf(Entity::class)
       ->and((string) $coach->getMember()->getName())
       ->toEqual('Jigoro Kano')
    ;
});
