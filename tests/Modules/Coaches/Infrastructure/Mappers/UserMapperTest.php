<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\Coaches\Domain\User;
use Kwai\Modules\Coaches\Infrastructure\Mappers\UserMapper;

it('can map data to a user', function () {
    $data = collect([
        'first_name' => 'Jigoro',
        'last_name' => 'Kano'
    ]);

    $user = UserMapper::toDomain($data);

    expect($user->getName()->__toString())
        ->toBe('Jigoro Kano');
});

it('can persist a user', function () {
    $user = new User(new Name('Jigoro', 'Kano'));

    $data = UserMapper::toPersistence($user);

    expect($data)
       ->toBeInstanceOf(Collection::class)
       ->toHaveKey('first_name', 'Jigoro')
       ->toHaveKey('last_name', 'Kano')
    ;
});
