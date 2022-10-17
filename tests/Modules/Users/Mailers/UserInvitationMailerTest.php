<?php
declare(strict_types=1);

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\LocalTimestamp;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Dependencies\Settings;
use Kwai\Core\Infrastructure\Dependencies\TemplateDependency;
use Kwai\Core\Infrastructure\Mailer\MailerServiceFactory;
use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Modules\Users\Domain\UserInvitation;
use Kwai\Modules\Users\Domain\UserInvitationEntity;
use Kwai\Modules\Users\Mailers\UserInvitationMailer;

it('can send an invitation', function() {
    $settings = depends('kwai.settings', Settings::class);
    $mailer = (new MailerServiceFactory(
        $settings->getMailerConfiguration()
    ))->create();

    $engine = depends('kwai.template', TemplateDependency::class);

    $mailer = new UserInvitationMailer(
        $mailer,
        new MailTemplate(
            $engine->createTemplate('Users::invitation_html'),
            $engine->createTemplate('Users::invitation_txt')
        ),
        new UserInvitationEntity(
            1,
            new UserInvitation(
                new EmailAddress('gella.vandecaveye@kwai.com'),
                new LocalTimestamp(Timestamp::createNow(), 'UTC'),
                'Gella Vandecaveye',
                new Creator(
                    1, new Name('Jigoro', 'Kano')
                )
            )
        )
    );
    $message = $mailer->send();
    expect($message)->not()->toBeNull();
});