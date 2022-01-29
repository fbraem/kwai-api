<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Role;
use Kwai\Modules\Users\Domain\Exceptions\RoleNotFoundException;

/**
 * Role repository interface
 */
interface RoleRepository
{
    /**
     * Get a role.
     *
     * @param  int $id Id of a role
     * @return Entity<Role>  A role
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @throws RoleNotFoundException
     * @throws RepositoryException
     */
    public function getById(int $id) : Entity;

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
     * @return Entity<Role>
     *@throws RepositoryException
     */
    public function create(Role $role): Entity;

    /**
     * Update the role
     *
     * @param Entity<Role> $role
     * @throws RepositoryException
     */
    public function update(Entity $role): void;

    /**
     * Factory method to create query
     *
     * @return RoleQuery
     */
    public function createQuery(): RoleQuery;
}
