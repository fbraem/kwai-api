<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserAccountNotFoundException;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\UserAccountEntity;

/**
 * Interface UserAccountRepository
 */
interface UserAccountRepository
{
    /**
     * Get a UserAccount using the email address.
     *
     * @param EmailAddress $email
     * @return UserAccountEntity
     * @throws RepositoryException
     * @throws UserAccountNotFoundException
     */
    public function get(EmailAddress $email): UserAccountEntity;

    /**
     * Get all user accounts.
     *
     * @param UserQuery|null $query
     * @param int|null       $limit
     * @param int|null       $offset
     * @return Collection<UserAccountEntity>
     * @throws RepositoryException
     */
    public function getAll(
        ?UserQuery $query = null,
        ?int $limit = null,
        ?int $offset = null
    ): Collection;

    /**
     * Update the login information.
     *
     * @param UserAccountEntity $account
     * @throws RepositoryException
     */
    public function update(UserAccountEntity $account): void;

    /**
     * Create a user account.
     *
     * @param UserAccount $account
     * @return UserAccountEntity
     * @throws RepositoryException
     */
    public function create(UserAccount $account): UserAccountEntity;

    /**
     * Checks if a user with the given email already exists
     *
     * @param EmailAddress $email
     * @throws RepositoryException
     * @return bool
     */
    public function existsWithEmail(EmailAddress $email): bool;

    /**
     * Factory method for a UserQuery
     *
     * @return UserQuery
     */
    public function createQuery(): UserQuery;
}
