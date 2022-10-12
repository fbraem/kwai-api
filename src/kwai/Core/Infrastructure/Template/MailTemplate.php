<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Template;

use Kwai\Core\Infrastructure\Mailer\Message;
use Kwai\Core\Infrastructure\Mailer\Recipients;
use Kwai\Core\Infrastructure\Mailer\SimpleMessage;

/**
 * MailTemplate
 */
class MailTemplate
{
    /**
     * MailTemplate constructor.
     * @param Template $htmlTemplate Template for generating HTML mail
     * @param Template $plainTextTemplate Template for generating plain text mail
     */
    public function __construct(
        private readonly Template $htmlTemplate,
        private readonly Template $plainTextTemplate
    ) {
    }

    public function createMessage(Recipients $recipients, string $subject, array $vars): Message
    {
        $message = new SimpleMessage($recipients, $subject);
        return $message
            ->withHtml($this->renderHtml($vars))
            ->withText($this->renderPlainText($vars))
        ;
    }

    /**
     * Renders the HTML template
     * @param array $vars
     * @return string
     */
    private function renderHtml(array $vars): string
    {
        return $this->htmlTemplate->render($vars);
    }

    /**
     * Renders the plain text template
     * @param array $vars
     * @return string
     */
    private function renderPlainText(array $vars): string
    {
        return $this->plainTextTemplate->render($vars);
    }
}
