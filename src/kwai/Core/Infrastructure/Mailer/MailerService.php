<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Mailer;

use Kwai\Modules\Mails\Domain\ValueObjects\Address;

/**
 * Interface for a mailer service
 */
interface MailerService
{
    /**
     * Send a message.
     *
     * @param Message $message
     * @param array $recipients
     * @param Address|null $from
     * @return void
     */
    public function send(Message $message, array $recipients = [], ?Address $from = null): void;
}
