<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Mailer;

use Kwai\Modules\Mails\Domain\Recipient;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;

/**
 * Interface for a mailer service
 */
interface MailerService
{
    /**
     * Returns the from address
     *
     * @return Address
     */
    public function getFrom(): Address;

    /**
     * Send a message.
     *
     * @param Message $message
     * @return void
     */
    public function send(Message $message): void;
}
