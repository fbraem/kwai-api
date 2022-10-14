<?php
declare(strict_types=1);

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\LocalTimestamp;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Dependencies\Settings;
use Kwai\Core\Infrastructure\Dependencies\TemplateDependency;
use Kwai\Core\Infrastructure\Mailer\MailerServiceFactory;
use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserEntity;
use Kwai\Modules\Users\Domain\UserRecovery;
use Kwai\Modules\Users\Domain\UserRecoveryEntity;
use Kwai\Modules\Users\Mailers\UserRecoveryMailer;

it('can send a recovery mail', function() {
    $settings = depends('kwai.settings', Settings::class);
    $mailer = (new MailerServiceFactory(
        $settings->getMailerConfiguration()
    ))->create();

    $engine = depends('kwai.template', TemplateDependency::class);

    $mailer = new UserRecoveryMailer(
        $mailer,
        new MailTemplate(
            $engine->createTemplate('Users::recover_html'),
            $engine->createTemplate('Users::recover_txt')
        ),
        new UserRecoveryEntity(
            1,
            new UserRecovery(
                new UniqueId(),
                new LocalTimestamp(
                    Timestamp::createFromDateTime(
                        new DateTime("now +2 hours")
                    ),
                    'UTC'
                ),
                new UserEntity(
                    1,
                    new User(
                        new UniqueId(),
                        new EmailAddress('jigoro.kano@kwai.com'),
                        new Name('Jigoro', 'Kano')
                    )
                )
            ),
        )
    );
    $message = $mailer->send();
    expect($message)->not()->toBeNull();
});
