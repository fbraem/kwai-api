<?php
/**
 * @package Kwai/Modules
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Domain\ValueObjects;

use Kwai\Core\Domain\EmailAddress;

/**
 * Valueobject for mail content
 */
final class MailContent
{
    /**
     * Subject
     * @var string
     */
    private $subject;

    /**
     * HTML
     * @var string
     */
    private $html;

    /**
     * Plain text
     * @var string
     */
    private $text;

    /**
     * Constructor
     * @param string $subject [description]
     * @param string $text    [description]
     * @param string $html    [description]
     */
    public function __construct(
        string $subject,
        string $text,
        string $html = ''
    ) {
        $this->subject = $subject;
        $this->text = $text;
        $this->html = $html;
    }

    /**
     * Returns the HTML content
     * @return string
     */
    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * Returns true when there is HTML content available
     * @return bool
     */
    public function hasHtml(): bool
    {
        return strlen($this->html) > 0;
    }

    /**
     * Returns the plain text of the content
     * @return string [description]
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Returns the subject
     * @return string
     */
    public function getSubject(): string
    {
        return $this->text;
    }
}
