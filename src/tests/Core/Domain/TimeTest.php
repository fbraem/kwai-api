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
