<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Role;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Domain\User;

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
     */
    public function getById(int $id) : Entity;

    /**
     * Get the user with the given unique id.
     *
     * @param UniqueId $uuid
     * @return Entity<User>
     * @throws UserNotFoundException
     * @throws RepositoryException
     */
    public function getByUniqueId(UniqueId $uuid) : Entity;

    /**
     * Get all users.
     *
     * @param UserQuery|null $query
     * @param int|null       $limit
     * @param int|null       $offset
     * @return Collection
     * @throws RepositoryException
     */
    public function getAll(
        ?UserQuery $query = null,
        ?int $limit = null,
        ?int $offset = null
    ): Collection;

    /**
     * Creates a UserQuery
     *
     * @return UserQuery
     */
    public function createQuery(): UserQuery;

    /**
     * Update a user
     *
     * @param Entity $user
     * @throws RepositoryException
     */
    public function update(Entity $user): void;

    /**
     * Link the roles to the given user.
     *
     * @param Entity                   $user
     * @param Collection<Entity<Role>> $roles
     * @return void
     *@throws RepositoryException
     */
    public function insertRoles(Entity $user, Collection $roles): void;

    /**
     * Remove the roles from the given user.
     *
     * @param Entity                   $user
     * @param Collection<Entity<Role>> $roles
     * @return void
     *@throws RepositoryException
     */
    public function deleteRoles(Entity $user, Collection $roles): void;
}
