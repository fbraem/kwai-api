<?php
/**
 * Testcase for Date
 */
declare(strict_types=1);

namespace Tests\Core\Domain;

use Kwai\Core\Domain\ValueObjects\Date;

it('can create a valid date', function () {
    $date = Date::createFromDate();
    expect($date)->toBeInstanceOf(Date::class);
});

it('can create a date for the first day of the year', function () {
    $date = Date::createFromDate(2019);
    expect(strval($date))->toBe('2019-01-01');
});

it('can create a date for the first day of a month', function () {
    $date = Date::createFromDate(2019, 2);
    expect(strval($date))->toBe('2019-02-01');
});

it('can create a date', function () {
    $date = Date::createFromDate(2019, 2, 1);
    expect(strval($date))->toBe('2019-02-01');
});
