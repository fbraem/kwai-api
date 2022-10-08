<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Mailers;

use Kwai\Core\Infrastructure\Mailer\MailerService;
use Kwai\Core\Infrastructure\Mailer\TemplatedMessage;
use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Modules\Mails\Domain\Mail;
use Kwai\Modules\Mails\Domain\Recipient;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Domain\ValueObjects\RecipientType;
use Kwai\Modules\Mails\Repositories\MailRepository;
use Kwai\Modules\Users\Domain\UserAccountEntity;
use Kwai\Modules\Users\Domain\UserRecoveryEntity;

/**
 * Class UserRecoveryMailer
 *
 * Mails a recovery code to the user
 */
class UserRecoveryMailer
{
    public function __construct(
        private readonly MailerService $mailer,
        private readonly MailTemplate $template,
        private readonly MailRepository $mailRepo,
        private readonly UserRecoveryEntity $recovery,
        private readonly UserAccountEntity $account
    ) {
    }

    public function send(): void
    {
        $message = new TemplatedMessage(
            template: $this->template,
            vars: [
                'uuid' => (string) $this->recovery->getUuid(),
                'name' => (string) $this->account->getUser()->getUsername(),
                'expires' => '',
            ]
        );
        $recipients = [
            new Recipient(
                type: RecipientType::TO,
                address: new Address(
                    $this->recovery->getReceiver(),
                    (string) $this->account->getUser()->getUsername()
                )
            )
        ];
        $this->mailRepo->create(
            new Mail(
                uuid: $this->$this->recovery->getUuid(),
                content: $message->createMailContent(),
                tag: 'Users.recover',
                recipients: collect($recipients)
            )
        );

        $this->mailer->send(
            $message,
            $recipients
        );
    }
}