<?php
declare(strict_types=1);

namespace Tests\Modules\Mails\Infstrastucture\Mailer;

use Kwai\Core\Infrastructure\Dependencies\Settings;
use Kwai\Core\Infrastructure\Mailer\MailerException;
use Kwai\Core\Infrastructure\Mailer\MailerServiceFactory;
use Kwai\Core\Infrastructure\Mailer\SimpleMessage;
use Kwai\Modules\Mails\Domain\Recipient;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Domain\ValueObjects\RecipientType;

it('can send a mail', function () {
    $settings = depends('kwai.settings', Settings::class);
    $mailer = (new MailerServiceFactory(
        $settings->getMailerConfiguration()
    ))->create();

    $mailer->send(
        new SimpleMessage(
            'Hello',
            'World',
        ),
        [
            new Recipient(
                RecipientType::TO,
                Address::create(['jigoro.kano@kwai.com' => 'Jigoro Kano'])
            ),
            new Recipient(
                RecipientType::CC,
                Address::create(['gella.vandecaveye@kwai.com' => 'Gella Vandecaveye'])
            )
        ]
    );
})->expectNotToPerformAssertions();

it('can overrule the sender', function () {
    $settings = depends('kwai.settings', Settings::class);
    $mailer = (new MailerServiceFactory(
        $settings->getMailerConfiguration()
    ))->create();

    $mailer->send(
        new SimpleMessage(
            'Hello',
            'World'
        ),
        [
            new Recipient(
                RecipientType::TO,
                Address::create(['ingrid.berghmans@kwai.com' => 'Ingrid Berghmans'])
            )
        ],
        Address::create(['jigoro.kano@kwai.com' => 'Jigoro Kano'])
    );
})->expectNotToPerformAssertions();

it('fails when no recipient is set', function () {
    $settings = depends('kwai.settings', Settings::class);
    $mailer = (new MailerServiceFactory(
        $settings->getMailerConfiguration()
    ))->create();

    $mailer->send(
        new SimpleMessage(
            'Hello',
            'World'
        ),
        [
            new Recipient(
            RecipientType::BCC,
            Address::create(['jigoro.kano@kwai.com' => 'Jigoro Kano'])
            )
        ]
    );
})->throws(MailerException::class);
