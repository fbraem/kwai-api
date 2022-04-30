<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\DomainEntity;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

/**
* RefreshToken entity
 */
final class RefreshToken implements DomainEntity
{
    /**
     * Constructor
     *
     * @param TokenIdentifier    $identifier
     * @param Timestamp          $expiration
     * @param AccessTokenEntity  $accessToken
     * @param bool               $revoked
     * @param TraceableTime|null $traceableTime
     */
    public function __construct(
        private readonly TokenIdentifier $identifier,
        private readonly Timestamp $expiration,
        private readonly AccessTokenEntity $accessToken,
        private bool $revoked = false,
        private readonly ?TraceableTime $traceableTime = new TraceableTime()
    ) {
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
     * @return AccessTokenEntity
     */
    public function getAccessToken(): AccessTokenEntity
    {
        return $this->accessToken;
    }
}
