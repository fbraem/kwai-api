<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Ability;
use Kwai\Modules\Users\Domain\Exceptions\AbilityNotFoundException;

/**
 * Ability repository interface
 */
interface AbilityRepository
{
    /**
     * Get an ability.
     *
     * @param  int $id Id of an ability
     * @throws RepositoryException
     * @throws AbilityNotFoundException
     * @return Entity<Ability>  An ability
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getById(int $id) : Entity;

    /**
     * Get all abilities
     *
     * @param AbilityQuery|null $query
     * @param int|null          $limit
     * @param int|null          $offset
     * @return Collection
     * @throws QueryException
     */
    public function getAll(
        ?AbilityQuery $query = null,
        ?int $limit = null,
        ?int $offset = 0
    ): Collection;

    /**
     * Create a new ability entity
     *
     * @param Ability $ability
     * @throws RepositoryException
     * @return Entity<Ability>
     */
    public function create(Ability $ability): Entity;

    /**
     * Update the ability
     *
     * @param Entity<Ability> $ability
     * @throws RepositoryException
     */
    public function update(Entity $ability): void;
}
