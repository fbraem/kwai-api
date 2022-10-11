<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Mailer;

use Kwai\Core\Infrastructure\Template\MailTemplate;

/**
 * Class MessageTemplateFactory
 *
 * Create a Message using a template.
 */
class MessageTemplateFactory
{
    public function __construct(
        private readonly Recipients $recipients,
        private readonly MailTemplate $template,
        private readonly array $vars = [],
    ) {
    }

    /**
     * @return Recipients
     */
    public function getRecipients(): Recipients
    {
        return $this->recipients;
    }

    public function createMessage(
        ?Recipients $recipients = null,
        array $vars = [],
        array $headers = []
    ): Message {
        $allVariables = array_merge($this->vars, $vars);
        return new SimpleMessage(
            $recipients ?? $this->recipients,
            $this->template->getSubject(),
            $this->template->renderPlainText($allVariables),
            $this->template->renderHtml($allVariables),
            $headers
        );
    }
}