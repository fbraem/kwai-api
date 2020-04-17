<?php
/**
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;

/**
 * UserInvitation Entity
 */
class UserInvitation implements DomainEntity
{
    /**
     * A UUID of the invitation.
     */
    private UniqueId $uuid;

    /**
     * The email address of the invited user
     */
    private EmailAddress $emailAddress;

    /**
     * Track create & modify times
     */
    private TraceableTime $traceableTime;

    /**
     * The timestamp when the invitation expires
     */
    private Timestamp $expiration;

    /**
     * A remark about the invitation
     */
    private string $remark;

    /**
     * The name of the invited user
     */
    private string $name;

    /**
     * The creator of the invitation.
     * @var Entity<User>
     */
    private Entity $creator;

    /**
     * Is this invitation revoked?
     */
    private bool $revoked;

    /**
     * Timestamp of confirmation
     */
    private ?Timestamp $confirmation;

    /**
     * Constructor.
     * @param object $props User Invitation properties
     */
    public function __construct(object $props)
    {
        $this->uuid = $props->uuid;
        $this->emailAddress = $props->emailAddress;
        $this->traceableTime = $props->traceableTime;
        $this->expiration = $props->expiration;
        $this->remark = $props->remark ?? '';
        $this->name = $props->name;
        $this->creator = $props->creator;
        $this->revoked = $props->revoked ?? false;
        $this->confirmation = $props->confirmation ?? null;
    }

    /**
     * Returns the email address.
     */
    public function getEmailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }

    /**
     * Get the last login timestamp
     */
    public function getExpiration(): ?Timestamp
    {
        return $this->expiration;
    }

    /**
     * Returns true when the invitation is expired
     */
    public function isExpired(): bool
    {
        return $this->expiration->isPast();
    }

    /**
     * Get the created_at/updated_at timestamps
     */
    public function getTraceableTime(): TraceableTime
    {
        return $this->traceableTime;
    }

    /**
     * Get the remark
     */
    public function getRemark(): string
    {
        return $this->remark;
    }

    /**
     * Get the unique id of the invitation
     */
    public function getUuid(): UniqueId
    {
        return $this->uuid;
    }

    /**
     * Get the name of the invited user
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

    /**
     * Returns true when the invitation is revoked.
     */
    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    /**
     * Returns true when the invitation is not revoked and not expired.
     */
    public function isValid(): bool
    {
        return !$this->isRevoked() && !$this->isExpired();
    }

    /**
     * Get the unique id of the invitation.
     */
    public function getUniqueId(): UniqueId
    {
        return $this->uuid;
    }

    /**
     * Is this invitation confirmed?
     */
    public function isConfirmed(): bool
    {
        return $this->confirmation != null;
    }

    /**
     * Get timestamp of confirmation
     */
    public function getConfirmation(): ?Timestamp
    {
        return $this->confirmation;
    }

    /**
     * Confirm the invitation by setting the confirmation timestamp to the current time.
     */
    public function confirm(): void
    {
        $this->confirmation = Timestamp::createNow();
    }
}
