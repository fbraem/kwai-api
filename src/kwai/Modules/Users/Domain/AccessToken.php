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
use TheSeer\Tokenizer\Token;

/**
* AccessToken entity
 */
class AccessToken implements DomainEntity
{
    /**
     * A unique identifier for the token.
     */
    private TokenIdentifier $identifier;

    /**
     * Timestamp when the token expires
     */
    private Timestamp $expiration;

    /**
     * Is this token revoked?
     */
    private bool $revoked;

    /**
     * Track create & modify times
     */
    private TraceableTime $traceableTime;

    /**
     * The user that owns the token.
     * @var Entity<UserAccount>
     */
    private Entity $account;

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
        $this->account = $props->account;
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
     * Get the owner of this token
     * @return Entity<UserAccount>
     */
    public function getUserAccount(): Entity
    {
        return $this->account;
    }
}
