<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Template;

/**
 * MailTemplate
 */
class MailTemplate
{
    /**
     * The subject of the mail
     */
    private string $subject;

    /**
     * Template for generating a HTML mail
     */
    private Template $htmlTemplate;

    /**
     * Template for generating a plain text mail
     */
    private Template $plainTextTemplate;

    /**
     * MailTemplate constructor.
     * @param string $subject The subject of the mail
     * @param Template $htmlTemplate Template for generating HTML mail
     * @param Template $plainTextTemplate Template for generating plain text mail
     */
    public function __construct(string $subject, Template $htmlTemplate, Template $plainTextTemplate)
    {
        $this->subject = $subject;
        $this->htmlTemplate = $htmlTemplate;
        $this->plainTextTemplate = $plainTextTemplate;
    }

    /**
     * Renders the HTML template
     * @param array $vars
     * @return string
     */
    public function renderHtml(array $vars): string
    {
        return $this->htmlTemplate->render($vars);
    }

    /**
     * Renders the plain text template
     * @param array $vars
     * @return string
     */
    public function renderPlainText(array $vars): string
    {
        return $this->htmlTemplate->render($vars);
    }

    /**
     * Returns the subject
     */
    public function getSubject(): string
    {
        return $this->subject;
    }
}
