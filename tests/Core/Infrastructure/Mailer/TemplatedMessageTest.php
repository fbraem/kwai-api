<?php
declare(strict_types=1);

use Kwai\Core\Infrastructure\Mailer\TemplatedMessage;
use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Core\Infrastructure\Template\PlatesEngine;
use Symfony\Component\Mime\Email;

it('can create a mail from a template', function() {
    $engine = new PlatesEngine(__DIR__);
    $message = new TemplatedMessage(
        new MailTemplate(
            'Test',
            $engine->createTemplate('mail_html_template'),
            $engine->createTemplate('mail_text_template')
        ),
        [
            'name' => 'Jigoro Kano'
        ]
    );

    $email = new Email();
    $email = $message->processMail($email);
    expect($email->getSubject())
        ->toBe('Test')
        ->and($email->getHtmlBody())
        ->toBe('<div>Hello Jigoro Kano</div>')
        ->and($email->getTextBody())
        ->toBe('Hello Jigoro Kano')
    ;
});