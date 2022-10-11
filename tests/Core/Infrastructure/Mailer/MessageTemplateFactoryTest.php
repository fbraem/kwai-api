<?php
declare(strict_types=1);

use Kwai\Core\Infrastructure\Mailer\MessageTemplateFactory;
use Kwai\Core\Infrastructure\Mailer\Recipient;
use Kwai\Core\Infrastructure\Mailer\Recipients;
use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Core\Infrastructure\Template\PlatesEngine;

it('can create a mail from a template', function() {
    $engine = new PlatesEngine(__DIR__);
    $recipients = new Recipients(
        new Recipient('jigoro.kano@kwai.com'),
        [ new Recipient('gella.vandecaveye@kwai.com') ]
    );
    $message = (new MessageTemplateFactory(
        $recipients,
        new MailTemplate(
            'Test',
            $engine->createTemplate('mail_html_template'),
            $engine->createTemplate('mail_text_template')
        ),
        [
            'name' => 'Jigoro Kano'
        ]
    ))->createMessage();

    expect($message->getSubject())
        ->toBe('Test')
        ->and($message->getHtml())
        ->toBe('<div>Hello Jigoro Kano</div>')
        ->and($message->getText())
        ->toBe('Hello Jigoro Kano')
    ;
});