<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\LocalTimestamp;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\UniqueId;

/**
 * Class UserRecovery
 *
 * Represents information for a user to reset the password.
 */
final class UserRecovery
{
    public function __construct(
        private readonly UniqueId $uuid,
        private readonly EmailAddress $receiver,
        private readonly LocalTimestamp $expiration,
        private readonly UserEntity $user,
        private readonly ?string $remark = null,
        private readonly ?Timestamp $confirmation = null,
        private readonly ?TraceableTime $traceableTime = new TraceableTime()
    ) {
    }

    /**
     * @return UniqueId
     */
    public function getUuid(): UniqueId
    {
        return $this->uuid;
    }

    /**
     * @return LocalTimestamp
     */
    public function getExpiration(): LocalTimestamp
    {
        return $this->expiration;
    }

    /**
     * Returns true when the recovery is expired
     */
    public function isExpired(): bool
    {
        return $this->expiration->getTimestamp()->isPast();
    }

    /**
     * @return string|null
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * @return Timestamp|null
     */
    public function getConfirmation(): ?Timestamp
    {
        return $this->confirmation;
    }

    /**
     * @return TraceableTime|null
     */
    public function getTraceableTime(): ?TraceableTime
    {
        return $this->traceableTime;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmation != null;
    }

    public function getUser(): UserEntity
    {
        return $this->user;
    }

    public function getReceiver(): EmailAddress
    {
        return $this->receiver;
    }
}