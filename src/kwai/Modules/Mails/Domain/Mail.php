<?php
/**
 * @package Kwai/Modules
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Domain;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Domain\ValueObjects\MailContent;

/**
 * Mail Entity
 */
class Mail implements DomainEntity
{
    /**
     * Constructor
     *
     * @param string|null        $tag
     * @param UniqueId           $uuid
     * @param Address            $sender
     * @param MailContent        $content
     * @param Timestamp|null     $sentTime
     * @param string|null        $remark
     * @param Creator            $creator
     * @param TraceableTime|null $traceableTime
     * @param Collection|null    $recipients
     */
    public function __construct(
        private UniqueId $uuid,
        private Address $sender,
        private MailContent $content,
        private Creator $creator,
        private ?Timestamp $sentTime = null,
        private ?string $remark = null,
        private ?TraceableTime $traceableTime = null,
        private ?string $tag = null,
        private ?Collection $recipients = null
    ) {
        $this->traceableTime ??= new TraceableTime();
        $this->recipients ??= new Collection();
    }

    /**
     * Add recipient
     * @param Entity $recipient
     * @param-phpstan Entity<Recipient> $recipient
     */
    public function addRecipient(Entity $recipient): void
    {
        $this->recipients[] = $recipient;
    }

    /**
     * Return the unique id
     */
    public function getUniqueId()
    {
        return $this->uuid;
    }

    /**
     * Get the sender
     */
    public function getSender(): Address
    {
        return $this->sender;
    }

    /**
     * Return the sent timestamp
     */
    public function getSentTime(): ?Timestamp
    {
        return $this->sentTime;
    }

    /**
     * Returns true when the mail was sent.
     */
    public function isSent(): bool
    {
        return $this->sentTime != null;
    }

    /**
     * Get the content
     */
    public function getContent(): MailContent
    {
        return $this->content;
    }

    /**
     * Get the associated tag
     */
    public function getTag(): ?string
    {
        return $this->tag;
    }

    /**
     * Get the remark
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * Get the creator of this mail
     */
    public function getCreator(): Creator
    {
        return $this->creator;
    }

    /**
     * Get the traceable timestamps
     */
    public function getTraceableTime(): TraceableTime
    {
        return $this->traceableTime;
    }

    /**
     * Get the recipients
     * @return Collection
     */
    public function getRecipients(): Collection
    {
        return $this->recipients->collect();
    }
}
