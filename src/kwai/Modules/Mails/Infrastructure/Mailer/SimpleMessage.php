<?php
/**
 * @package Modules
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Mailer;

use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Symfony\Component\Mime\Email;

/**
 * An email message
 */
class SimpleMessage implements Message
{
    /**
     * Create a new message
     *
     * @param string       $subject
     * @param string       $body
     * @param Address|null $from
     */
    public function __construct(
        private string $subject,
        private string $body,
        private ?Address $from = null
    ) {
    }

    /**
    * @inheritdoc
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
    * @inheritdoc
    */
    public function createMessage(): Email
    {
        return (new Email())
            ->subject($this->subject)
            ->text($this->body);
    }

    /**
     * @inheritdoc
     */
    public function getFrom(): ?Address
    {
       return $this->from;
    }
}
