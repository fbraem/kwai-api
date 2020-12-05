<?php

use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\Trainings\Domain\Coach;

it('can create a coach', function () {
    $coach = new Coach(
        name: new Name('Jigoro', 'Kano')
    );
    expect((string) $coach->getName())
        ->toBe('Jigoro Kano')
    ;
});
