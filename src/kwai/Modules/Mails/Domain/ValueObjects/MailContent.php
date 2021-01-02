<?php
/**
 * @package Modules
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Domain\ValueObjects;

/**
 * Valueobject for mail content
 */
final class MailContent
{
    /**
     * MailContent constructor.
     * @param string $subject [description]
     * @param string $text    [description]
     * @param string $html    [description]
     */
    public function __construct(
        private string $subject,
        private string $text,
        private string $html = ''
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
