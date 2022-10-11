<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Mailer;

/**
 * Interface for a mailer service
 */
interface MailerService
{
    /**
     * Send a message.
     *
     * @param Message $message
     * @return void
     */
    public function send(Message $message): void;
}
