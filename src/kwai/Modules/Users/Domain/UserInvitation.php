<?php
/**
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\DomainEntity;

/**
 * UserInvitation Entity
 */
class UserInvitation implements DomainEntity
{
    /**
     * Constructor.
     *
     * @param UniqueId           $uuid
     * @param EmailAddress       $emailAddress
     * @param Timestamp          $expiration
     * @param string             $remark
     * @param string             $name
     * @param Creator            $creator
     * @param bool               $revoked
     * @param TraceableTime|null $traceableTime
     * @param Timestamp|null     $confirmation
     */
    public function __construct(
        private UniqueId $uuid,
        private EmailAddress $emailAddress,
        private Timestamp $expiration,
        private string $remark,
        private string $name,
        private Creator $creator,
        private bool $revoked = false,
        private ?TraceableTime $traceableTime = null,
        private ?Timestamp $confirmation = null,
    ) {
        $this->remark ??= '';
        $this->traceableTime ??= new TraceableTime();
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
     *
     * @return Creator
     */
    public function getCreator(): Creator
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
