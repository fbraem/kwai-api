<?php
/**
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\DateTime;
use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\DomainEntity;

/**
* AccessToken entity
 */
class AccessToken implements DomainEntity
{
    /**
     * A unique identifier for the token.
     * @var Identifier
     */
    private $identifier;

    /**
     * Timestamp when the token expires
     * @var DateTime
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
     * Constructor
     * @param object $props
     */
    public function __construct(object $props)
    {
        $this->identifier = $props->identifier;
        $this->expiration = $props->expiration;
        $this->revoked = $props->revoked;
        $this->traceableTime = $props->traceableTime;
    }

    /**
     * Revoke this token
     */
    public function revoke() : void
    {
        $this->revoked = true;
    }
}
