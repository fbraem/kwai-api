<?php
/**
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\Timestamp;
use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\DomainEntity;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

/**
* RefreshToken entity
 */
class RefreshToken implements DomainEntity
{
    /**
     * A unique identifier for the token.
     * @var TokenIdentifier
     */
    private $identifier;

    /**
     * Timestamp when the token expires
     * @var Timestamp
     */
    private $expiration;

    /**
     * Is this token revoked?
     * @var bool
     */
    private $revoked;

    /**
     * Track create & modify times
     * @var TraceableTime
     */
    private $traceableTime;

    /**
     * Associated AccessToken
     * @var Entity<AccessToken>
     */
    private $accessToken;

    /**
     * Constructor
     * @param object $props
     */
    public function __construct(object $props)
    {
        $this->identifier = $props->identifier;
        $this->expiration = $props->expiration;
        $this->revoked = $props->revoked ?? false;
        $this->traceableTime = $props->traceableTime ?? new TraceableTime();
        $this->accessToken = $props->accessToken;
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
     * @return Entity<AccessToken>
     */
    public function getAccessToken(): ?Entity
    {
        return $this->accessToken;
    }
}
