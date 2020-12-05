<?php

declare(strict_types=1);

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Time;
use Kwai\Core\Domain\ValueObjects\Weekday;
use Kwai\Modules\Trainings\Domain\Definition;

$creator = new Creator(
    1,
    new Name('Jigoro', 'Kano')
);

it('can construct a definition', function () use ($creator) {
    $definition = new Definition(
        name: 'Wednesday Training',
        description: 'We train each wednesday evening',
        weekday: Weekday::WEDNESDAY(),
        startTime: new Time(20, 0, 'Europe/Brussels'),
        endTime: new Time(21, 0, 'Europe/Brussels'),
        creator: $creator,
    );
    expect($definition->getDescription())
        ->toBe('We train each wednesday evening')
    ;
});
