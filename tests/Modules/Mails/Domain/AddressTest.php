<?php
declare(strict_types=1);

namespace Tests\Modules\Mails\Domain;

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;

it('can create an address without a name', function () {
    $address = new Address(
        new EmailAddress('test@kwai.com')
    );
    expect($address)
        ->toBeInstanceOf(Address::class);
    expect(strval($address->getEmail()))
        ->toBe('test@kwai.com');
    expect($address->getName())
        ->toBe('');
});

it('can create an address with an email and name', function () {
    $address = new Address(
        new EmailAddress('test@kwai.com'),
        'Test'
    );
    expect($address)
        ->toBeInstanceOf(Address::class);
    expect(strval($address->getEmail()))
        ->toBe('test@kwai.com');
    expect($address->getName())
        ->toBe('Test');
});
