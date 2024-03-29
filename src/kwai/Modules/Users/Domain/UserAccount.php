<?php
/**
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\Exceptions\NotAllowedException;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\DomainEntity;

use Kwai\Modules\Users\Domain\ValueObjects\Password;

/**
 * User Account Entity. A user with account information like password,
 * revoked, ...
 */
final class UserAccount implements DomainEntity
{
    /**
     * Constructor.
     *
     * @param User            $user
     * @param Password        $password
     * @param Timestamp|null  $lastLogin
     * @param Timestamp|null  $lastUnsuccessfulLogin
     * @param bool            $revoked
     */
    public function __construct(
        private readonly User $user,
        private Password $password,
        private ?Timestamp $lastLogin = null,
        private ?Timestamp $lastUnsuccessfulLogin = null,
        private bool $revoked = false,
    ) {
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
     * Checks if the user is revoked.
     * @return bool
     */
    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    /**
     * Returns the user.
     *
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
     * Checks if the user has logged in before
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

    public function getPassword(): Password
    {
        return $this->password;
    }

    /**
     * @throws NotAllowedException
     */
    public function resetPassword(Password $password): void
    {
        if ($this->isRevoked()) {
            throw new NotAllowedException(
                'User Account',
                'ResetPassword',
                'Reset password on revoked account is not allowed'
            );
        }
        $this->password = $password;
    }
}
