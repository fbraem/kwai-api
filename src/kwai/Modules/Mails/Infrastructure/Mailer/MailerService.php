<?php
/**
 * @package Kwai
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Mailer;

/**
 * Interface for a mailer service
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
interface MailerService
{
    /**
     * Send a message
     * @param Message $message
     * @param array $to
     * @param array $cc
     * @param array $bcc
     */
    public function send(
        Message $message,
        array $to,
        array $cc,
        array $bcc
    ): void;
}
