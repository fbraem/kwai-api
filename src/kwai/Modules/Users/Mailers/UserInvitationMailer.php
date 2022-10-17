<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Mailers;

use Kwai\Core\Domain\Mailer;
use Kwai\Core\Infrastructure\Mailer\MailerService;
use Kwai\Core\Infrastructure\Mailer\Message;
use Kwai\Core\Infrastructure\Mailer\Recipient;
use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Modules\Users\Domain\UserInvitationEntity;

/**
 * Class UserInvitationMailer
 */
class UserInvitationMailer implements Mailer
{
    public function __construct(
        private readonly MailerService $mailer,
        private readonly MailTemplate $template,
        private readonly UserInvitationEntity $invitation
    ) {
    }

    public function send(): Message
    {
        $recipients = $this->mailer->createRecipients();
        $message = $this->template->createMessage(
            $recipients->withTo(
                new Recipient(
                    (string) $this->invitation->getEmailAddress(),
                    (string) $this->invitation->getName()
                )
            ),
            'User Invitation',
            [
                'uuid' => $this->invitation->getUniqueId(),
                'name' => (string) $this->invitation->getName(),
                'expires' => $this->invitation->getExpiration()->getTimestamp()->diffInDays()
            ]
        );

        $this->mailer->send($message);

        return $message;
    }
}