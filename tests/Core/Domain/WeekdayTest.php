<?php
declare(strict_types=1);

use Kwai\Core\Domain\ValueObjects\Weekday;

it('can create a weekday', function () {
    $weekday = Weekday::from(1);
    expect($weekday)
        ->toEqual(Weekday::MONDAY)
    ;
});
