<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Ability;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
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
     * @throws UserNotFoundException
     * @throws RepositoryException
     * @return Entity<User>
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getById(int $id) : Entity;

    /**
     * Get all users.
     *
     * @param UserQuery|null $query
     * @param int|null       $limit
     * @param int|null       $offset
     * @return Collection
     * @throws QueryException
     */
    public function getAll(
        ?UserQuery $query = null,
        ?int $limit = null,
        ?int $offset = null
    ): Collection;

    /**
     * Create a new user account.
     *
     * @param UserAccount $account
     * @throws RepositoryException
     * @return Entity
     */
    public function create(UserAccount $account): Entity;

    /**
     * Add the ability to the user.
     *
     * @param Entity<Ability> $user
     * @param Entity<Ability> $ability
     * @throws RepositoryException
     * @return Entity<User>
     */
    public function addAbility(Entity $user, Entity $ability): Entity;

    /**
     * Remove the ability from the user.
     *
     * @param Entity<Ability> $user
     * @param Entity<Ability> $ability
     * @throws RepositoryException
     * @return Entity<User>
     */
    public function removeAbility(Entity $user, Entity $ability): Entity;

    /**
     * Creates a UserQuery
     *
     * @return UserQuery
     */
    public function createQuery(): UserQuery;

    /**
     * Checks if a user with the given email already exists
     *
     * @param EmailAddress $email
     * @throws RepositoryException
     * @return bool
     */
    public function existsWithEmail(EmailAddress $email): bool;
}
