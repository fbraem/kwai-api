<?php
declare(strict_types=1);

use Kwai\Core\Infrastructure\Mailer\SimpleMessage;

it('can build a simple message', function() {
    $message = new SimpleMessage(
        'Test', 'This is a test message'
    );
    $email = new \Symfony\Component\Mime\Email();
    $email = $message->processMail($email);
    expect($email->getSubject())
        ->toBe('Test')
        ->and($email->getTextBody())
        ->toBe('This is a test message')
    ;
});
