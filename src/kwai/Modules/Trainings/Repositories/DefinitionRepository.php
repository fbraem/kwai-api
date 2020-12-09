<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Exceptions\DefinitionNotFoundException;
use Kwai\Modules\Trainings\Domain\Definition;

/**
 * Interface DefinitionRepository
 */
interface DefinitionRepository
{
    /**
     * Get the definition with the given id
     *
     * @param int $id
     * @return Entity<Definition>
     *@throws RepositoryException
     * @throws DefinitionNotFoundException
     */
    public function getById(int $id): Entity;

    /**
     * Create a query
     *
     * @return DefinitionQuery
     */
    public function createQuery(): DefinitionQuery;

    /**
     * Executes the query and returns a Collection with entities.
     * For each record, the mapper will be called to create an entity.
     *
     * @param DefinitionQuery $query
     * @param int|null        $limit
     * @param int|null        $offset
     * @return Collection
     * @throws QueryException
     */
    public function getAll(DefinitionQuery $query, ?int $limit, ?int $offset): Collection;

    /**
     * Save a new definition
     *
     * @param Definition $definition
     * @return Entity<Definition>
     * @throws RepositoryException
     */
    public function create(Definition $definition): Entity;

    /**
     * Updates a definition
     *
     * @param Entity<Definition> $definition
     * @throws RepositoryException
     */
    public function update(Entity $definition): void;

    /**
     * Removes a definition
     *
     * @param Entity $definition
     * @throws RepositoryException
     */
    public function remove(Entity $definition): void;
}
