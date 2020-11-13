<?php
/**
 * @package Kwai
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Mailer;

use Swift_Message;

/**
 * An email message
 */
class SimpleMessage implements Message
{
    /**
     * The subject
     */
    private string $subject;

    /**
     * The message body
     */
    private string $body;

    /**
     * Create a new message
     * @param string $subject
     * @param string $body
     */
    public function __construct(
        string $subject,
        string $body
    ) {
        $this->subject = $subject;
        $this->body = $body;
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
    public function createMessage(): Swift_Message
    {
        return (new Swift_Message($this->getSubject()))->setBody($this->body);
    }
}
