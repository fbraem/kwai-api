<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Mailer;

use Symfony\Component\Mime\Email;

/**
 * Interface for an email message
 */
interface Message
{
    public function getRecipients(): Recipients;
    public function withRecipients(Recipients $recipients): self;

    public function getSubject(): string;
    public function withSubject(string $subject): self;

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array;
    public function withHeaders(array $headers): self;

    public function getHtml(): ?string;
    public function withHtml(string $html): self;

    public function getText(): ?string;
    public function withText(string $text): self;
}
