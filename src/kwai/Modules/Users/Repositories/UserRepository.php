<?php
/**
 * User Repository interface
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\EmailAddress;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserAccount;

/**
 * User repository interface
 */
interface UserRepository
{
    /**
     * Get the user with the given id
     * @param  int    $id
     * @return Entity<User>
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getById(int $id) : Entity;

    /**
     * Get the user with the given uuid
     * @param  UniqueId $uid
     * @return Entity<User>
     * @throws NotFoundException
     */
    public function getByUUID(UniqueId $uid) : Entity;

    /**
     * Get the user with the given emailaddress.
     * @param  EmailAddress $email
     * @return Entity<User>
     * @throws NotFoundException
     */
    public function getByEmail(EmailAddress $email) : Entity;

    /**
     * Checks if a user with the given emailaddress exists
     * @param  EmailAddress $email
     * @return bool
     */
    public function existsWithEmail(EmailAddress $email) : bool;

    /**
     * Get the user account with the given emailaddress.
     * @param  EmailAddress $email
     * @return Entity<UserAccount>
     * @throws NotFoundException
     */
    public function getAccount(EmailAddress $email) : Entity;

    /**
     * Get the user associated with the given token identifier
     * (from an accesstoken).
     * @param  TokenIdentifier $token
     * @return Entity<User>
     * @throws NotFoundException
     */
    public function getByAccessToken(TokenIdentifier $token) : Entity;

    /**
     * Update the login information
     * @param Entity<UserAccount> $account
     */
    public function updateAccount(Entity $account): void;
}
