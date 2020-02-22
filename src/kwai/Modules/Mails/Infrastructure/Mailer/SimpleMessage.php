<?php
/**
 * @package Kwai
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Mailer;

use Genkgo\Mail\MessageInterface;
use Genkgo\Mail\PlainTextMessage;

/**
 * An email message
 */
class SimpleMessage implements Message
{
    /**
     * The subject
     * @var string
     */
    private $subject;

    /**
     * The message body
     * @var string
     */
    private $body;

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
    public function createMessage(): MessageInterface
    {
        return new PlainTextMessage($this->body);
    }
}
