<?php
/**
 * Testcase for UniqueId
 */
declare(strict_types=1);

namespace Tests\Core\Domain;

use InvalidArgumentException;
use Kwai\Core\Domain\ValueObjects\UniqueId;

it('can create a unique id', function () {
    $uuid = new UniqueId();
    expect($uuid)->toBeInstanceOf(UniqueId::class);
});

it('throws an exception for an invalid uuid', function () {
    new UniqueId('invalid');
})->throws(InvalidArgumentException::class);

it('throws an exception for an invalid version', function () {
    new UniqueId('e4eaaaf2-d142-11e1-b3e4-080027620cdd');
})->throws(InvalidArgumentException::class);
