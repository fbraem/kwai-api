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
 * User Account Entity. A user with account information like password,
 * revoked, ...
 */
class UserAccount implements DomainEntity
{
    /**
     * User
     * @var Entity<User>
     */
    private $user;

    /**
     * The timestamp of the last login
     * @var Timestamp
     */
    private $lastLogin;

    /**
     * TODO: add to table
     * The last unsuccessful login
     */
    private Timestamp $lastUnsuccessfulLogin;

    /**
     * The password of the user
     */
    private Password $password;

    /**
     * Is the user revoked?
     */
    private bool $revoked;

    /**
     * The abilities of the user.
     * @var Ability[]
     */
    private array $abilities;

    /**
     * Constructor.
     * @param  object $props User properties
     */
    public function __construct(object $props)
    {
        $this->user = $props->user;
        $this->lastLogin = $props->lastLogin ?? null;
        $this->lastUnsuccessfulLogin = $props->lastUnsuccessfulLogin ?? null;
        $this->revoked = $props->revoked ?? false;
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
            if ($this->password->verify($password)) {
                $this->lastLogin = Timestamp::createNow();
                return true;
            }
        }
        $this->lastUnsuccessfulLogin = Timestamp::createNow();
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
     * Returns the user.
     * @return Entity
     * @phpstan-param Entity<User>
     */
    public function getUser(): Entity
    {
        return $this->user;
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
