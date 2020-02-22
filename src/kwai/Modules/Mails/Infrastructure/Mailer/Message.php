<?php
/**
 * @package Kwai
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Mailer;

use Genkgo\Mail\MessageInterface;

/**
 * Interface for a email message
 */
interface Message
{
    /**
     * Return the subject
     */
    public function getSubject(): string;

    /**
     * Create a Genkgo message
     * @return MessageInterface
     */
    public function createMessage(): MessageInterface;
}
