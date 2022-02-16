<?php
/**
 * @package Modules
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Mailer;

use Symfony\Component\Mime\Email;

/**
 * Interface for an email message
 */
interface Message
{
    /**
     * Return the subject
     * @return string
     */
    public function getSubject(): string;

    /**
     * Create a Email message
     *
     * @return Email
     */
    public function createMessage(): Email;
}
