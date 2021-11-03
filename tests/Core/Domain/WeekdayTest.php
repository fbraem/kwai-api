<?php
declare(strict_types=1);

use Kwai\Core\Domain\ValueObjects\Weekday;

it('can create a weekday', function () {
    $weekday = new Weekday(1);
    expect($weekday)
        ->toEqual(Weekday::MONDAY())
    ;
});

it('throws an exception for a wrong day', function () {
    /** @noinspection PhpExpressionResultUnusedInspection */
    new Weekday(10);
})->throws(UnexpectedValueException::class);
