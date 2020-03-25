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
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserAccount;

/**
 * User repository interface
 */
interface UserRepository
{
    /**
     * Get the user with the given id.
     *
     * @param  int    $id
     * @throws NotFoundException
     * @throws RepositoryException
     * @return Entity<User>
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getById(int $id) : Entity;

    /**
     * Get the user with the given uuid.
     *
     * @param  UniqueId $uid
     * @throws NotFoundException
     * @throws RepositoryException
     * @return Entity<User>
     */
    public function getByUUID(UniqueId $uid) : Entity;

    /**
     * Get the user with the given email address.
     *
     * @param  EmailAddress $email
     * @throws NotFoundException
     * @throws RepositoryException
     * @return Entity<User>
     */
    public function getByEmail(EmailAddress $email) : Entity;

    /**
     * Checks if a user with the given email address exists
     *
     * @param  EmailAddress $email
     * @throws RepositoryException
     * @return bool
     */
    public function existsWithEmail(EmailAddress $email) : bool;

    /**
     * Get the user account with the given email address.
     *
     * @param EmailAddress $email
     * @throws NotFoundException
     * @throws RepositoryException
     * @return Entity
     */
    public function getAccount(EmailAddress $email) : Entity;

    /**
     * Get all users.
     *
     * @return Entity[]
     * @throws RepositoryException
     */
    public function getAll(): array;

    /**
     * Get the user associated with the given token identifier
     * (from an accesstoken).
     *
     * @param  TokenIdentifier $token
     * @throws NotFoundException
     * @throws RepositoryException
     * @return Entity<User>
     */
    public function getByAccessToken(TokenIdentifier $token) : Entity;

    /**
     * Update the login information.
     *
     * @param Entity<UserAccount> $account
     * @throws RepositoryException
     */
    public function updateAccount(Entity $account): void;

    /**
     * Create a new user account.
     *
     * @param UserAccount $account
     * @throws RepositoryException
     * @return Entity
     */
    public function create(UserAccount $account): Entity;
}
