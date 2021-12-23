<?php
/**
 * Testcase for Password
 */
declare(strict_types=1);

namespace Tests\Modules\Users\Domain;

use Kwai\Modules\Users\Domain\ValueObjects\Password;

it('can create a password from a string', function () {
    $password = Password::fromString('hajime');
    expect($password)
        ->toBeInstanceOf(Password::class)
    ;
    return strval($password);
});

it('can verify a password', function ($hashedPassword) {
    $password = new Password($hashedPassword);
    expect($password->verify('hajime'))
        ->toBe(true);
})
    ->depends('it can create a password from a string');
