<?php
declare(strict_types=1);

namespace Tests\Modules\Mails\Infstrastucture\Mailer;

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Infrastructure\Mailer\MailerServiceFactory;

use Kwai\Modules\Mails\Infrastructure\Mailer\SimpleMessage;
use Kwai\Modules\Mails\Infrastructure\Mailer\MailerException;

it('can send a mail', function () {
    $mailer = (new MailerServiceFactory())->create(
        $_ENV['smtp']
    );

    $result = $mailer->send(
        new SimpleMessage('Hello', 'World'),
        new Address(new EmailAddress($_ENV['from'])),
        ['jigoro.kano@kwai.com' => 'Jigoro Kano']

    );
    expect($result)
        ->toBe(1)
    ;
});

it('fails when no recipient is set', function () {
    $mailer = (new MailerServiceFactory())->create(
        $_ENV['smtp']
    );

    $mailer->send(
        new SimpleMessage('Hello', 'World'),
        new Address(new EmailAddress($_ENV['from'])),
        []
    );
})->throws(MailerException::class);
