<?php
declare(strict_types=1);

namespace Tests\Modules\Mails\Infstrastucture\Mailer;

use Kwai\Core\Infrastructure\Dependencies\Settings;
use Kwai\Core\Infrastructure\Mailer\MailerException;
use Kwai\Core\Infrastructure\Mailer\MailerServiceFactory;
use Kwai\Core\Infrastructure\Mailer\Recipient;
use Kwai\Core\Infrastructure\Mailer\Recipients;
use Kwai\Core\Infrastructure\Mailer\SimpleMessage;

it('can send a mail', function () {
    $settings = depends('kwai.settings', Settings::class);
    $mailer = (new MailerServiceFactory(
        $settings->getMailerConfiguration()
    ))->create();

    $mailer->send(
        new SimpleMessage(
            new Recipients(
                new Recipient('jigoro.kano@kwai.com'),
                [ new Recipient('gella.vandecaveye@kwai.com', 'Gella Vandecaveye') ]
            ),
            'Hello',
            'World',
        )
    );
})->expectNotToPerformAssertions();

it('fails when no recipient is set', function () {
    $settings = depends('kwai.settings', Settings::class);
    $mailer = (new MailerServiceFactory(
        $settings->getMailerConfiguration()
    ))->create();

    $mailer->send(
        new SimpleMessage(
            new Recipients(
                new Recipient('jigoro.kano@kwai.com'),
                []
            ),
            'Hello',
            'World',
        )
    );
})->throws(MailerException::class);
