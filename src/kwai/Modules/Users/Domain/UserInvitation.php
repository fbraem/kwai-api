<?php
/**
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\Timestamp;
use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;

/**
 * UserInvitation Entity
 */
class UserInvitation implements DomainEntity
{
    /**
     * A UUID of the invitation.
     * @var UniqueId
     */
    private $uuid;

    /**
     * The emailaddress of the invited user
     * @var EmailAddress
     */
    private $emailAddress;

    /**
     * Track create & modify times
     * @var TraceableTime
     */
    private $traceableTime;

    /**
     * The timestamp when the invitation expires
     * @var Timestamp
     */
    private $expiration;

    /**
     * A remark about the invitation
     * @var string
     */
    private $remark;

    /**
     * The name of the invited user
     * @var string
     */
    private $name;

    /**
     * The user that sends the invitation.
     * @var Entity<User>
     */
    private $creator;

    /**
     * Constructor.
     * @param  object $props User Invitation properties
     */
    public function __construct(object $props)
    {
        $this->uuid = $props->uuid;
        $this->emailAddress = $props->emailAddress;
        $this->traceableTime = $props->traceableTime;
        $this->expiration = $props->expiration;
        $this->remark = $props->remark;
        $this->name = $props->name;
        $this->creator = $props->creator;
    }

    /**
     * Returns the email address.
     * @return EmailAddress
     */
    public function getEmailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }

    /**
     * Get the last login timestamp
     * @return Timestamp
     */
    public function getExpiration(): ?Timestamp
    {
        return $this->expiration;
    }

    /**
     * Returns true when the invitation is expired
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expiration->isPast();
    }

    /**
     * Get the created_at/updated_at timestamps
     * @return TraceableTime
     */
    public function getTraceableTime(): TraceableTime
    {
        return $this->traceableTime;
    }

    /**
     * Get the remark
     * @return string
     */
    public function getRemark(): string
    {
        return $this->remark;
    }

    /**
     * Get the unique id of the invitation
     * @return UniqueId
     */
    public function getUuid(): UniqueId
    {
        return $this->uuid;
    }

    /**
     * Get the name of the invited user
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the user that created this invitation.
     * @return Entity<User>
     */
    public function getCreator(): Entity
    {
        return $this->creator;
    }
}
