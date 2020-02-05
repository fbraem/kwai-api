<?php
/**
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\Timestamp;
use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;

use Kwai\Modules\Users\Domain\ValueObjects\Password;
use Kwai\Modules\Users\Domain\ValueObjects\Username;

/**
 * User Entity
 */
class User implements DomainEntity
{
    /**
     * A UUID of the user.
     * @var UniqueId
     */
    private $uuid;

    /**
     * The emailaddress of the user
     * @var EmailAddress
     */
    private $emailAddress;

    /**
     * Track create & modify times
     * @var TraceableTime
     */
    private $traceableTime;

    /**
     * The timestamp of the last login
     * @var Timestamp
     */
    private $lastLogin;

    /**
     * A remark about the user
     * @var string
     */
    private $remark;

    /**
     * The username
     * @var Username
     */
    private $username;

    /**
     * The password of the user
     * @var Password
     */
    private $password;

    /**
     * Is the user revoked?
     * @var bool
     */
    private $revoked;

    /**
     * The abilities of the user.
     * @var Ability[]
     */
    private $abilities;

    /**
     * Constructor.
     * @param  object $props User properties
     */
    public function __construct(object $props)
    {
        $this->uuid = $props->uuid;
        $this->emailAddress = $props->emailAddress;
        $this->traceableTime = $props->traceableTime;
        $this->lastLogin = $props->lastLogin ?? null;
        $this->remark = $props->remark;
        $this->revoked = $props->revoked ?? false;
        $this->username = $props->username ?? null;
        $this->password = $props->password ?? null;
        $this->abilities = $props->abilities ?? [];
    }

    /**
     * Verify the password. When no password is set, false will be returned.
     * @param string $password The password to login.
     * @return bool
     */
    public function login(string $password): bool
    {
        if ($this->password) {
            return $this->password->verify($password);
        }
        return false;
    }

    /**
     * TODO: Add revoked to table
     * Checks if the user is revoked.
     * @return bool
     */
    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    /**
     * Return the abilities of this user.
     * @return Ability[]
     */
    public function getAbilities(): array
    {
        return $this->abilities;
    }

    /**
     * Returns the email address.
     * @return EmailAddress
     */
    public function getEmailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }

    /**
     * Get the last login timestamp
     * @return Timestamp
     */
    public function getLastLogin(): ?Timestamp
    {
        return $this->lastLogin;
    }

    /**
     * Get the created_at/updated_at timestamps
     * @return TraceableTime
     */
    public function getTraceableTime(): TraceableTime
    {
        return $this->traceableTime;
    }

    /**
     * Get the remark
     * @return string
     */
    public function getRemark(): string
    {
        return $this->remark;
    }

    /**
     * Get the unique id of the user
     * @return UniqueId
     */
    public function getUuid(): UniqueId
    {
        return $this->uuid;
    }

    /**
     * Get the username
     * @return Username
     */
    public function getUsername(): ?Username
    {
        return $this->username;
    }

    /**
     * Checks if the user has loggedin before
     * @return bool
     */
    public function hasLastLogin(): bool
    {
        return $this->lastLogin != null;
    }

    /**
     * Adds an ability to this user.
     * @param Entity<Ability> $ability
     */
    public function addAbility(Entity $ability)
    {
        $this->abilities[] = $ability;
    }
}
