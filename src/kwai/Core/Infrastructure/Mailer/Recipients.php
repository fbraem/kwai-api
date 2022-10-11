<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Mailer;

/**
 * Class Recipients
 */
class Recipients
{
    public function __construct(
        private readonly Identity $from,
        private readonly array    $to = [],
        private readonly array    $cc = [],
        private readonly array    $bcc = []
    ) {
    }

    /**
     * @return Identity
     */
    public function getFrom(): Identity
    {
        return $this->from;
    }

    /**
     * @return array
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * @return array
     */
    public function getCc(): array
    {
        return $this->cc;
    }

    /**
     * @return array
     */
    public function getBcc(): array
    {
        return $this->bcc;
    }

    public function withFrom(Identity $from): self
    {
        return new self(
            $from,
            $this->to,
            $this->cc,
            $this->bcc
        );
    }

    public function withTo(Identity ...$to): self
    {
        return new self(
            $this->from,
            $to,
            $this->cc,
            $this->bcc
        );
    }

    public function addTo(Identity ...$to): self
    {
        return new self(
            $this->from,
            array_merge($this->to, $to),
            $this->cc,
            $this->bcc
        );
    }

    public function withCc(Identity ...$cc): self
    {
        return new self(
            $this->from,
            $this->to,
            $cc,
            $this->bcc
        );
    }

    public function addCc(Identity ...$cc): self
    {
        return new self(
            $this->from,
            $this->to,
            array_merge($this->cc, $cc),
            $this->bcc
        );
    }

    public function withBcc(Identity ...$bcc): self
    {
        return new self(
            $this->from,
            $this->to,
            $this->cc,
            $bcc
        );
    }

    public function addBcc(Identity ...$bcc): self
    {
        return new self(
            $this->from,
            $this->to,
            $this->cc,
            array_merge($this->bcc, $bcc)
        );
    }
}