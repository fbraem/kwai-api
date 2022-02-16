<?php
/**
 * @package Modules
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Mailer;

use Symfony\Component\Mime\Email;

/**
 * An email message
 */
class SimpleMessage implements Message
{
    /**
     * Create a new message
     * @param string $subject
     * @param string $body
     */
    public function __construct(
        private string $subject,
        private string $body
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
}
