<?php
declare(strict_types=1);

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;

it('can create an Address', function () {
    $address = new Address(
        new EmailAddress('jigoro.kano@kwai.com'),
        'Jigoro Kano'
    );
    expect((string) $address->getEmail())
        ->toEqual('jigoro.kano@kwai.com')
        ->and($address->getName())
        ->toEqual('Jigoro Kano')
    ;
});

it('can create an Address with associative array', function () {
    $address = Address::create(
        ['jigoro.kano@kwai.com' => 'Jigoro Kano']
    );
    expect((string) $address->getEmail())
        ->toEqual('jigoro.kano@kwai.com')
        ->and($address->getName())
        ->toEqual('Jigoro Kano')
    ;
});

it('can create an Address with an array', function () {
    $address = Address::create([
        'jigoro.kano@kwai.com',
            'Jigoro Kano'
    ]);
    expect((string) $address->getEmail())
        ->toEqual('jigoro.kano@kwai.com')
        ->and($address->getName())
        ->toEqual('Jigoro Kano')
    ;
});

it('can create an Address with a string', function () {
    $address = Address::create('jigoro.kano@kwai.com');
    expect((string) $address->getEmail())
        ->toEqual('jigoro.kano@kwai.com')
    ;
});
