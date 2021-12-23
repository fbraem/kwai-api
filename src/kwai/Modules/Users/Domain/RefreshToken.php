<?php
/**
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\DomainEntity;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

/**
* RefreshToken entity
 */
class RefreshToken implements DomainEntity
{
    /**
     * Constructor
     *
     * @param TokenIdentifier    $identifier
     * @param Timestamp          $expiration
     * @param Entity             $accessToken
     * @param bool               $revoked
     * @param TraceableTime|null $traceableTime
     */
    public function __construct(
        private TokenIdentifier $identifier,
        private Timestamp $expiration,
        private Entity $accessToken,
        private bool $revoked = false,
        private ?TraceableTime $traceableTime = null,
    ) {
        $this->traceableTime ??= new TraceableTime();
    }

    /**
     * Revoke this token
     */
    public function revoke() : void
    {
        $this->revoked = true;
    }

    /**
     * Get identifier
     * @return TokenIdentifier
     */
    public function getIdentifier(): TokenIdentifier
    {
        return $this->identifier;
    }

    /**
     * Get expiration
     * @return Timestamp
     */
    public function getExpiration(): Timestamp
    {
        return $this->expiration;
    }

    /**
     * Returns true when the refreshToken is expired.
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expiration->isPast();
    }

    /**
     * Is this token revoked?
     * @return bool
     */
    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    /**
     * Get traceable time
     * @return TraceableTime
     */
    public function getTraceableTime(): TraceableTime
    {
        return $this->traceableTime;
    }

    /**
     * Get the associated accesstoken
     *
     * @return Entity|null
     */
    public function getAccessToken(): ?Entity
    {
        return $this->accessToken;
    }
}
