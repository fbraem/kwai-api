<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Role;
use Kwai\Modules\Users\Domain\Exceptions\RoleNotFoundException;
use Kwai\Modules\Users\Domain\RoleEntity;

/**
 * Role repository interface
 */
interface RoleRepository
{
    /**
     * Get a role.
     *
     * @param int $id Id of a role
     * @return RoleEntity A role
     * @throws RoleNotFoundException
     * @throws RepositoryException
     */
    public function getById(int $id) : RoleEntity;

    /**
     * Get all roles
     *
     * @param RoleQuery|null $query
     * @param int|null       $limit
     * @param int|null       $offset
     * @return Collection
     * @throws RepositoryException
     */
    public function getAll(
        ?RoleQuery $query = null,
        ?int       $limit = null,
        ?int       $offset = 0
    ): Collection;

    /**
     * Create a new role entity
     *
     * @param Role $role
     * @return RoleEntity
     * @throws RepositoryException
     */
    public function create(Role $role): RoleEntity;

    /**
     * Update the role
     *
     * @param RoleEntity $role
     * @throws RepositoryException
     */
    public function update(RoleEntity $role): void;

    /**
     * Factory method to create query
     *
     * @return RoleQuery
     */
    public function createQuery(): RoleQuery;
}
