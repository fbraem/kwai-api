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
     * Returns the from address
     *
     * @return Address
     */
    public function getFrom(): Address;

    /**
     * Send a message.
     *
     * @param Message        $message
     * @param array<Address> $to
     * @param array<Address> $cc
     * @param array<Address> $bcc
     * @return void
     */
    public function send(
        Message $message,
        array $to,
        array $cc = [],
        array $bcc = []
    ): void;
}
