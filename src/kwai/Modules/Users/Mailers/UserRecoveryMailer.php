<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Mailers;

use Kwai\Core\Infrastructure\Mailer\MailerService;
use Kwai\Core\Infrastructure\Mailer\MessageTemplateFactory;
use Kwai\Core\Infrastructure\Mailer\Recipient;
use Kwai\Core\Infrastructure\Mailer\Recipients;
use Kwai\Core\Infrastructure\Template\MailTemplate;
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

    public function send(Recipients $recipients): void
    {
        $message = $this->template->createMessage(
            $recipients->withTo(
                new Recipient(
                    (string) $this->recovery->getReceiver(),
                    (string) $this->account->getUser()->getUsername()
                )
            ),
            'Recover User Password',
            [
                'uuid' => (string) $this->recovery->getUuid(),
                'name' => (string) $this->account->getUser()->getUsername(),
                'expires' => '',
            ]
        );
        /*
        $this->mailRepo->create(
            new Mail(
                uuid: $this->$this->recovery->getUuid(),
                content: $message->createMailContent(),
                tag: 'Users.recover',
                recipients: collect($recipients)
            )
        );
        */

        $this->mailer->send($message);
    }
}