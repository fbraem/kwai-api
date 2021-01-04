<?php
/**
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;

use Kwai\Modules\Users\Domain\ValueObjects\Password;

/**
 * User Account Entity. A user with account information like password,
 * revoked, ...
 */
class UserAccount implements DomainEntity
{
    /**
     * Constructor.
     *
     * @param User            $user
     * @param Password        $password
     * @param Timestamp|null  $lastLogin
     * @param Timestamp|null  $lastUnsuccessfulLogin
     * @param bool            $revoked
     * @param Collection|null $abilities
     */
    public function __construct(
        private User $user,
        private Password $password,
        private ?Timestamp $lastLogin = null,
        private ?Timestamp $lastUnsuccessfulLogin = null,
        private bool $revoked = false,
        private ?Collection $abilities = null
    ) {
        $this->abilities ??= collect();
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
     *
     * @return Collection
     */
    public function getAbilities(): Collection
    {
        return $this->abilities->collect();
    }

    /**
     * Returns the user.
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Get the last login timestamp
     *
     * @return Timestamp|null
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
     * Get the last unsuccessful login timestamp
     *
     * @return Timestamp|null
     */
    public function getLastUnsuccessfulLogin(): ?Timestamp
    {
        return $this->lastUnsuccessfulLogin;
    }

    /**
     * Adds an ability to this user.
     * @param Entity<Ability> $ability
     */
    public function addAbility(Entity $ability)
    {
        $this->abilities->push($ability);
    }

    public function getPassword(): Password
    {
        return $this->password;
    }
}
