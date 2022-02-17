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

    /**
     * Returns a From Address. This allows to override the used from field
     * of the MailerConfiguration. When null is returned, the value of
     * MailerConfiguration is used.
     * @return Address|null
     */
    public function getFrom(): ?Address;
}
