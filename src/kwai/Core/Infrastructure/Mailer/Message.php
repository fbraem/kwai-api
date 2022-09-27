<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Mailer;

use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Domain\ValueObjects\MailContent;
use Symfony\Component\Mime\Email;

/**
 * Interface for an email message
 */
interface Message
{
    /**
     * @return MailContent
     */
    public function createMailContent(): MailContent;

    /**
     * Returns a From Address. This allows to override the used from field
     * of the MailerConfiguration. When null is returned, the value of
     * MailerConfiguration is used.
     * @return Address|null
     */
    public function getFrom(): ?Address;

    public function getRecipients(): array;
}
