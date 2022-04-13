<?php
/**
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use DateTime;
use Kwai\Core\Domain\Exceptions\NotAllowedException;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\LocalTimestamp;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;

/**
 * UserInvitation Entity
 */
class UserInvitation
{
    /**
     * Constructor.
     *
     * @param EmailAddress       $emailAddress
     * @param LocalTimestamp     $expiration
     * @param string             $name
     * @param Creator            $creator
     * @param string|null        $remark
     * @param UniqueId           $uuid
     * @param bool               $revoked
     * @param TraceableTime      $traceableTime
     * @param Timestamp|null     $confirmation
     */
    public function __construct(
        private EmailAddress $emailAddress,
        private LocalTimestamp $expiration,
        private string $name,
        private Creator $creator,
        private ?string $remark = null,
        private UniqueId $uuid = new UniqueId(),
        private bool $revoked = false,
        private TraceableTime $traceableTime = new TraceableTime(),
        private ?Timestamp $confirmation = null,
    ) {
    }

    /**
     * Returns the email address.
     */
    public function getEmailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }

    /**
     * Get the time when the invitation will expire
     */
    public function getExpiration(): LocalTimestamp
    {
        return $this->expiration;
    }

    /**
     * Returns true when the invitation is expired
     */
    public function isExpired(): bool
    {
        return $this->expiration->getTimestamp()->isPast();
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
    public function getRemark(): ?string
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

    /**
     * Renews the invitation (when it is not yet confirmed)
     *
     * @throws NotAllowedException
     */
    public function renew(int $expiry = 15): void
    {
        if ($this->isConfirmed()) {
            throw new NotAllowedException(
                'User invitation',
                'Renew',
                'A confirmed user invitation cannot be renewed'
            );
        }
        $this->expiration = new LocalTimestamp(
            Timestamp::createFromDateTime(
                new DateTime("now +{$expiry} days")
            ),
            'UTC'
        );
    }
}
