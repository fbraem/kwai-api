<?php
/**
 * Testcase for Password
 */
declare(strict_types=1);

namespace Tests\Modules\Users\Domain;

use Kwai\Modules\Users\Domain\ValueObjects\Password;
use InvalidArgumentException;

it('can create a password from a string', function () {
    $password = Password::fromString('Hajime123');
    expect($password)
        ->toBeInstanceOf(Password::class)
    ;
    return strval($password);
});

it('can verify a password', function ($hashedPassword) {
    $password = new Password($hashedPassword);
    expect($password->verify('Hajime123'))
        ->toBe(true);
})
    ->depends('it can create a password from a string');

it('checks for numbers in a password', function () {
    $password = Password::fromString('Hajime');
})->expectException(InvalidArgumentException::class);

it('checks for uppercase in a password', function () {
    $password = Password::fromString('hajime123');
})->expectException(InvalidArgumentException::class);

it('checks for lowercase in a password', function () {
    $password = Password::fromString('HAJIME123');
})->expectException(InvalidArgumentException::class);

