<?php
declare(strict_types=1);

use Kwai\Core\Domain\ValueObjects\Time;

it('can create a time', function () {
    $time = Time::createFromString('19:00:00', 'Europe/Brussels');
    expect($time->getTimezone())
       ->toBe('Europe/Brussels')
    ;
    expect($time->getHour())
       ->toBe(19)
    ;
    expect($time->getMinute())
       ->toBe(0)
    ;
});

it('throws an exception for an invalid hour', function () {
    new Time(24, 0, 'Europe/Brussels');
})
    ->throws(InvalidArgumentException::class)
;

it('throws an exception for an invalid minute', function () {
    new Time(12, 60, 'Europe/Brussels');
})
    ->throws(InvalidArgumentException::class)
;

it('can check if a time is before another time', function () {
    $startTime = new Time(20, 0, 'Europe/Brussels');
    $endTime = new Time(21, 0, 'Europe/Brussels');
    expect($startTime->isBefore($endTime))
        ->toBe(true)
    ;
    expect($endTime->isBefore($startTime))
        ->toBe(false)
    ;

    $endTime = new Time(20, 30, 'Europe/Brussels');
    expect($startTime->isBefore($endTime))
        ->toBe(true)
    ;
    expect($endTime->isBefore($startTime))
        ->toBe(false)
    ;
});

it('can format time', function () {
    $time = new Time(20, 0, 'Europe/Brussels');
    expect($time->format())
        ->toBe('20:00')
    ;
});
