<?php
/**
 * @package Kwai
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Mailer;

use Swift_Message;

/**
 * Interface for a email message
 */
interface Message
{
    /**
     * Return the subject
     * @return string
     */
    public function getSubject(): string;

    /**
     * Create a Swift_Message message
     * @return Swift_Message
     */
    public function createMessage(): Swift_Message;
}
