<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Domain\RoleEntity;
use Kwai\Modules\Users\Domain\UserEntity;

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
     * @return UserEntity
     */
    public function getById(int $id) : UserEntity;

    /**
     * Get the user with the given unique id.
     *
     * @param UniqueId $uuid
     * @return UserEntity
     * @throws UserNotFoundException
     * @throws RepositoryException
     */
    public function getByUniqueId(UniqueId $uuid) : UserEntity;

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
     * @param UserEntity $user
     * @throws RepositoryException
     */
    public function update(UserEntity $user): void;

    /**
     * Link the roles to the given user.
     *
     * @param UserEntity             $user
     * @param Collection<RoleEntity> $roles
     * @return void
     * @throws RepositoryException
     */
    public function insertRoles(UserEntity $user, Collection $roles): void;

    /**
     * Remove the roles from the given user.
     *
     * @param UserEntity             $user
     * @param Collection<RoleEntity> $roles
     * @return void
     * @throws RepositoryException
     */
    public function deleteRoles(UserEntity $user, Collection $roles): void;
}
