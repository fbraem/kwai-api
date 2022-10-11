<?php
declare(strict_types=1);

use Kwai\Core\Infrastructure\Mailer\Recipient;
use Kwai\Core\Infrastructure\Mailer\Recipients;
use Kwai\Core\Infrastructure\Mailer\SimpleMessage;

it('can build a simple message', function() {
    $message = new SimpleMessage(
        new Recipients(
            new Recipient('jigoro.kano@kwai.com'),
            [new Recipient('gella.vandecaveye@kwai.com')]
        ),
        'Test',
        'This is a test message'
    );
    expect($message->getSubject())
        ->toBe('Test')
        ->and($message->getText())
        ->toBe('This is a test message')
        ->and($message->getRecipients()->getFrom()->getEmail())
        ->toBe('jigoro.kano@kwai.com')
        ->and($message->getRecipients()->getTo())
        ->toBeArray()
        ->toHaveCount(1)
        ->and($message->getRecipients()->getTo()[0]->getEmail())
        ->toBe('gella.vandecaveye@kwai.com')
    ;
});
