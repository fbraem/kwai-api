<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Mailer;

use Kwai\Modules\Mails\Domain\ValueObjects\MailContent;

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

    public function createMailContent(): MailContent
    {
        return new MailContent(
            subject: $this->subject,
            text: $this->body
        );
    }
}
