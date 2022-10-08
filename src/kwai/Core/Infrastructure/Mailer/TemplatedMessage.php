<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Mailer;

use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Modules\Mails\Domain\ValueObjects\MailContent;

/**
 * Class TemplatedMessage
 */
class TemplatedMessage implements Message
{
    /**
     * @param MailTemplate $template
     * @param array $vars
     */
    public function __construct(
        private readonly MailTemplate $template,
        private readonly array $vars
    ) {
    }

    public function createMailContent(): MailContent
    {
        return new MailContent(
            subject: $this->template->getSubject(),
            text: $this->template->renderPlainText($this->vars),
            html: $this->template->renderHtml($this->vars)
        );
    }
}