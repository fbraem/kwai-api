<?php
/**
 * @package Modules
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Domain\ValueObjects;

/**
 * Value object for mail content
 */
final class MailContent
{
    /**
     * MailContent constructor.
     * @param string $subject Subject of the mail
     * @param string $text    Text content of the mail
     * @param string $html    HTML content of the mail
     */
    public function __construct(
        private readonly string $subject,
        private readonly string $text,
        private readonly string $html = ''
    ) {
    }

    /**
     * Returns the HTML content
     */
    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * Returns true when there is HTML content available
     */
    public function hasHtml(): bool
    {
        return strlen($this->html) > 0;
    }

    /**
     * Returns the plain text of the content
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Returns the subject
     */
    public function getSubject(): string
    {
        return $this->subject;
    }
}
