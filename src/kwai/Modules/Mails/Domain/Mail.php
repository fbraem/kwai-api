<?php
/**
 * @package Kwai/Modules
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Domain;

use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\Timestamp;
use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;

use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Domain\ValueObjects\MailContent;
use Kwai\Modules\Users\Domain\User;

/**
 * Mail Entity
 */
class Mail implements DomainEntity
{
    /**
     * Tag of the email
     */
    private ?string $tag;

    /**
     * Uniqued ID for this mail
     */
    private UniqueId $uuid;

    /**
     * Sender
     */
    private Address $sender;

    /**
     * Content of the mail
     */
    private MailContent $content;

    /**
     * Time of sending
     */
    private Timestamp $sentTime;

    /**
     * Remark
     */
    private ?string $remark;

    /**
     * User that created this mail
     * @var Entity<User>
     */
    private Entity $creator;

    /**
     * Track create & modify times
     */
    private TraceableTime $traceableTime;

    /**
     * The recipients
     * @var Entity<Recipient>[]
     */
    private array $recipients;

    /**
     * Constructor
     * @param object $props Mail properties
     */
    public function __construct(object $props)
    {
        $this->tag = $props->tag;
        $this->uuid = $props->uuid;
        $this->sender = $props->sender;
        $this->content = $props->content;
        $this->sentTime = $props->sentTime;
        $this->remark = $props->remark;
        $this->creator = $props->creator;
        $this->traceableTime = $props->traceableTime;
        $this->recipients = $props->recipients;
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
     * @phpstan-return Entity<User>
     */
    public function getCreator(): Entity
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
     * @return Recipient[]
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }
}
