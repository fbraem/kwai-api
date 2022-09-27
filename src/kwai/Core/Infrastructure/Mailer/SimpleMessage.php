<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Mailer;

use Kwai\Modules\Mails\Domain\Recipient;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Domain\ValueObjects\MailContent;

/**
 * An email message
 */
class SimpleMessage implements Message
{
    /**
     * Create a new message
     *
     * @param string $subject
     * @param string $body
     * @param Recipient[] $recipients
     * @param Address|null $from
     */
    public function __construct(
        private readonly string $subject,
        private readonly string $body,
        private readonly array $recipients,
        private readonly ?Address $from = null
    ) {
    }

    /**
     * @inheritdoc
     */
    public function getFrom(): ?Address
    {
       return $this->from;
    }

    /**
     * @return Recipient[]
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function createMailContent(): MailContent
    {
        return new MailContent(
            subject: $this->subject,
            text: $this->body
        );
    }
}
