<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Mailer;

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Mails\Domain\Mail;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Domain\ValueObjects\MailContent;
use Kwai\Modules\Mails\Domain\ValueObjects\RecipientType;
use Kwai\Modules\Mails\Repositories\MailRepository;
use Psr\Log\LoggerInterface;

/**
 * Class DatabaseMailerService
 *
 * A mailer service, that will also save the mail to the database
 */
class DatabaseMailerService implements MailerService
{
    public function __construct(
        private readonly MailRepository $mailRepo,
        private readonly MailerService $service
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getFrom(): Address
    {
        return $this->service->getFrom();
    }

    /**
     * @throws RepositoryException
     */
    public function sendMail(
        UniqueId $uuid,
        string $tag,
        TemplatedMessage $mail,
        array $recipients,
        ?Creator $creator = null,
    ): void {
        $this->mailRepo->create(
            new Mail(
                uuid: $uuid,
                sender: $mail->getFrom() ?? $this->service->getFrom(),
                content: $mail->createMailContent(),
                creator: $creator,
                tag: $tag,
                recipients: collect($recipients)
            )
        );
        $this->send($mail);
    }

    /**
     * @inheritDoc
     */
    public function send(Message $message): void
    {
        $this->service->send($message);
    }
}