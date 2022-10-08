<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Mailer;

use Kwai\Core\Infrastructure\Template\MailTemplate;
use Symfony\Component\Mime\Email;

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
        private array $vars = []
    ) {
    }

    public function addVariable(string $name, mixed $value): void
    {
        $this->vars[$name] = $value;
    }

    public function processMail(Email $email): Email
    {
        return $email
            ->subject($this->template->getSubject())
            ->html($this->template->renderHtml($this->vars))
            ->text($this->template->renderPlainText($this->vars))
        ;
    }
}