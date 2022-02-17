<?php
declare(strict_types=1);

namespace Tests\Modules\Mails\Infstrastucture\Mailer;

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Infrastructure\Configuration\DsnConfiguration;
use Kwai\Core\Infrastructure\Dependencies\Settings;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Infrastructure\Mailer\MailerConfiguration;
use Kwai\Modules\Mails\Infrastructure\Mailer\MailerServiceFactory;

use Kwai\Modules\Mails\Infrastructure\Mailer\SimpleMessage;
use Kwai\Modules\Mails\Infrastructure\Mailer\MailerException;

it('can send a mail', function () {
    $settings = (new Settings())->create();
    $mailer = (new MailerServiceFactory(
        new MailerConfiguration(
            DsnConfiguration::create(
                scheme: 'smtp',
                user: $settings['mail']['user'],
                pwd: $settings['mail']['pass'],
                host: $settings['mail']['host'],
                port: $settings['mail']['port']
            ),
            $settings['mail']['from']
        )
    ))->create();

    $mailer->send(
        new SimpleMessage('Hello', 'World'),
        [
            Address::create(['jigoro.kano@kwai.com' => 'Jigoro Kano'])
        ],
        [
            Address::create(['gella.vandecaveye@kwai.com' => 'Gella Vandecaveye'])
        ]
    );
})->expectNotToPerformAssertions();

it('can overrule the sender', function () {
    $settings = (new Settings())->create();
    $mailer = (new MailerServiceFactory(
        new MailerConfiguration(
            DsnConfiguration::create(
                scheme: 'smtp',
                user: $settings['mail']['user'],
                pwd: $settings['mail']['pass'],
                host: $settings['mail']['host'],
                port: $settings['mail']['port']
            ),
            $settings['mail']['from']
        )
    ))->create();

    $mailer->send(
        new SimpleMessage(
            'Hello',
            'World',
            Address::create(['ingrid.berghmans@kwai.com' => 'Ingrid Berghmans'])
        ), [
            Address::create(['jigoro.kano@kwai.com' => 'Jigoro Kano'])
        ]
    );
})->expectNotToPerformAssertions();

it('fails when no recipient is set', function () {
    $settings = (new Settings())->create();
    $mailer = (new MailerServiceFactory(
        new MailerConfiguration(
            DsnConfiguration::create(
                scheme: 'smtp',
                user: $settings['mail']['user'],
                pwd: $settings['mail']['pass'],
                host: $settings['mail']['host'],
                port: $settings['mail']['port']
            ),
            $settings['mail']['from']
        )
    ))->create();

    $mailer->send(
        new SimpleMessage('Hello', 'World'),
        []
    );
})->throws(MailerException::class);
