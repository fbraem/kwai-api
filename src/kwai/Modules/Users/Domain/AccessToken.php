<?php
/**
 * AccessToken entity
 *
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\DateTime;
use Kwai\Core\Domain\TraceableTime;

/**
 * AccessToken entity class
 */
class AccessToken
{
    /**
     * The id of the accesstoken.
     * @var int
     */
    private $id;

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

    private function __construct()
    {
    }

    /**
     * Returns the id of the accesstoken
     * @return int The id of the accesstoken
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * Revoke this token
     */
    public function revoke() : void
    {
        $this->revoked = true;
    }

    public static function create(object $props, int $id = null): self
    {
        $token = new self();
        $token->identifier = $props->identifier;
        $token->expiration = $props->expiration;
        $token->revoked = $props->revoked;
        $token->traceableTime = $props->traceableTime;
        if ($id) {
            $token->id = $id;
        }
        return $token;
    }
}
