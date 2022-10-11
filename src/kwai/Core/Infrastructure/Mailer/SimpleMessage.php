<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Mailer;

/**
 * An email message
 */
class SimpleMessage implements Message
{
    /**
     * Create a new message
     *
     * @param Recipients $recipients
     * @param string $subject
     * @param string $text
     * @param string $html
     * @param array $headers
     */
    public function __construct(
        private readonly Recipients $recipients,
        private readonly string $subject,
        private readonly string $text = '',
        private readonly string $html = '',
        private readonly array $headers = []
    ) {
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function withSubject(string $subject): self
    {
        return new self(
            clone($this->recipients),
            $subject,
            $this->text,
            $this->html,
            $this->headers,
        );
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function withHeaders(array $headers): self
    {
        return new self(
            clone($this->recipients),
            $this->subject,
            $this->text,
            $this->html,
            $headers
        );
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function withHtml(string $html): self
    {
        return new self(
            clone($this->recipients),
            $this->subject,
            $this->text,
            $html,
            $this->headers,
        );
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function withText(string $text): self
    {
        return new self(
            clone($this->recipients),
            $this->subject,
            $text,
            $this->html,
            $this->headers,
        );
    }

    public function getRecipients(): Recipients
    {
        return clone($this->recipients);
    }

    public function withRecipients(Recipients $recipients): self
    {
        return new self(
            clone($recipients),
            $this->subject,
            $this->text,
            $this->html,
            $this->headers,
        );
    }
}
