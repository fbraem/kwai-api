<?php
/**
 * Testcase for Timestamp
 */
declare(strict_types=1);

namespace Tests\Core\Domain;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Kwai\Core\Domain\ValueObjects\Timestamp;

it('can create a current timestamp', function () {
    CarbonImmutable::setTestNow(Carbon::create(2020, 2, 20, 20, 20, 20));
    $now = Timestamp::createNow();
    expect($now->format('Y-m-d'))->toBe('2020-02-20');
    CarbonImmutable::setTestNow(null);
});

it('can create a timestamp', function () {
    $time = Timestamp::create(2020, 2, 20, 20, 20, 20);
    expect(strval($time))->toBe('2020-02-20 20:20:20');
});

it('can create a timestamp from an object', function () {
    $date = Timestamp::createFromObject((object) [
        'year' => 2020,
        'month' => 1,
        'day' => 31
    ]);
    expect(strval($date))->toBe('2020-01-31 00:00:00');
});

it('can create a date for the current a year', function () {
    $currentYear = date('Y');
    $date = Timestamp::createFromObject((object) [
        'month' => 1,
        'day' => 31
    ]);
    expect(strval($date))->toBe("$currentYear-01-31 00:00:00");
});
