<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Mailer;

use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Modules\Mails\Domain\Recipient;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Domain\ValueObjects\MailContent;

/**
 * Class TemplatedMessage
 */
class TemplatedMessage implements Message
{
    /**
     * @param MailTemplate $template
     * @param array $vars
     * @param Recipient[] $recipients
     * @param Address|null $from
     */
    public function __construct(
        private readonly MailTemplate $template,
        private readonly array $vars,
        private readonly array $recipients,
        private readonly ?Address $from = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getFrom(): ?Address
    {
        return $this->from;
    }

    /**
     * @return Recipient[]
     */
    public function getRecipients(): array
    {
        return $this->recipients;
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