<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Mailer;

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Mails\Domain\Mail;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Repositories\MailRepository;

/**
 * Class DatabaseMailerService
 *
 * A mailer service, that will also save the mail to the database
 */
class DatabaseMailerService implements MailerService
{
    public function __construct(
        private readonly MailRepository $mailRepo,
        private readonly MailerService $service,
        private readonly UniqueId $uuid,
        private readonly string $tag,
        private readonly ?Creator $creator = null
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
    public function send(Message $message): void {
        $this->mailRepo->create(
            new Mail(
                uuid: $this->uuid,
                sender: $message->getFrom() ?? $this->service->getFrom(),
                content: $message->createMailContent(),
                creator: $this->creator,
                tag: $this->tag,
                recipients: collect($message->getRecipients())
            )
        );
        $this->service->send($message);
    }
}