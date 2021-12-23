<?php
/**
 * @package Modules
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Mailer;

use Kwai\Modules\Mails\Domain\ValueObjects\Address;

/**
 * Interface for a mailer service
 */
interface MailerService
{
    /**
     * Send a message. Returns the number of successfully sent messages.
     * @param Message $message
     * @param Address $from
     * @param array $to
     * @param array $cc
     * @param array $bcc
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @return int
     */
    public function send(
        Message $message,
        Address $from,
        array $to,
        array $cc = [],
        array $bcc = []
    ): int;
}
