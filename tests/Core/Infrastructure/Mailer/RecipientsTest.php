<?php
declare(strict_types=1);

use Kwai\Core\Infrastructure\Mailer\Recipient;
use Kwai\Core\Infrastructure\Mailer\Recipients;

it('can handle recipients', function() {
    $recipients = new Recipients(
        new Recipient('jigoro.kano@kwai.com'),
        [ new Recipient('gella.vandecaveye@kwai.com') ]
    );
    expect($recipients->getFrom()->getEmail())->toBe('jigoro.kano@kwai.com');

    $recipients = new Recipients(
        new Recipient('jigoro.kano@kwai.com', 'Jigoro Kano'),
        [ new Recipient('gella.vandecaveye@kwai.com') ]
    );
    expect($recipients->getFrom()->getEmail())->toBe('jigoro.kano@kwai.com');
});

it('is immutable', function() {
    $recipients = new Recipients(
        new Recipient('jigoro.kano@kwai.com'),
        [ new Recipient('gella.vandecaveye@kwai.com') ]
    );
    $newList = $recipients->withFrom(new Recipient('ingrid.berghmans@kwai.com'));
    expect($recipients->getFrom()->getEmail())
        ->toBe('jigoro.kano@kwai.com')
        ->and($newList->getFrom()->getEmail())
        ->toBe('ingrid.berghmans@kwai.com');
});

it('can handle multiple receivers', function() {
    $recipients = new Recipients(
        new Recipient('jigoro.kano@kwai.com'),
        [ new Recipient('gella.vandecaveye@kwai.com') ]
    );
    $newList = $recipients->addTo(new Recipient('ingrid.berghmans@kwai.com'));
    expect($newList->getTo())
        ->toHaveCount(2)
    ;
});