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
use Kwai\Modules\Users\Domain\UserRecoveryEntity;

/**
 * Class UserRecoveryMailer
 *
 * Mails a recovery code to the user
 */
class UserRecoveryMailer implements Mailer
{
    public function __construct(
        private readonly MailerService $mailer,
        private readonly MailTemplate $template,
        private readonly UserRecoveryEntity $recovery
    ) {
    }

    public function send(): Message
    {
        $recipients = $this->mailer->createRecipients();
        $message = $this->template->createMessage(
            $recipients->withTo(
                new Recipient(
                    (string) $this->recovery->getUser()->getEmailAddress(),
                    (string) $this->recovery->getUser()->getUsername()
                )
            ),
            'Recover User Password',
            [
                'uuid' => (string) $this->recovery->getUuid(),
                'name' => (string) $this->recovery->getUser()->getUsername(),
                'expires' => 2
            ]
        );
        $this->mailer->send($message);
        return $message;
    }
}