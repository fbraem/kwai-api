<?php
/**
 * Testcase for Template
 */
declare(strict_types=1);

namespace Tests\Core\Domain\Infrastructure;

use Kwai\Core\Infrastructure\Mailer\Recipient;
use Kwai\Core\Infrastructure\Mailer\Recipients;
use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Core\Infrastructure\Template\PlatesEngine;

it('can create a mail template', function () {
    $engine = new PlatesEngine(__DIR__);
    $mailTemplate = new MailTemplate(
        $engine->createTemplate('html_template'),
        $engine->createTemplate('template')
    );
    $message = $mailTemplate->createMessage(
        new Recipients(
            new Recipient('jigoro.kano@kwai.com')
        ),
        'Mail Template Test',
        [
            'name' => 'World'
        ]
    );
    expect($message->getText())
        ->toBe('Hello World')
        ->and($message->getHtml())
        ->toBe('Hello <b>World</b>');
});
