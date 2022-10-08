<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Mailer;

use Symfony\Component\Mime\Email;

/**
 * An email message
 */
class SimpleMessage implements Message
{
    /**
     * Create a new message
     *
     * @param string $subject
     * @param string $body
     */
    public function __construct(
        private readonly string $subject,
        private readonly string $body
    ) {
    }

    public function processMail(Email $email): Email
    {
        return $email
            ->subject($this->subject)
            ->text($this->body)
        ;
    }
}
