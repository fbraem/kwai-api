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
use Kwai\Modules\Trainings\Domain\DefinitionEntity;
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
     * @return DefinitionEntity
     * @throws RepositoryException
     * @throws DefinitionNotFoundException
     */
    public function getById(int $id): DefinitionEntity;

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
     * @param DefinitionQuery|null $query
     * @param int|null             $limit
     * @param int|null             $offset
     * @return Collection
     * @throws RepositoryException
     */
    public function getAll(?DefinitionQuery $query = null, ?int $limit = null, ?int $offset = null): Collection;

    /**
     * Save a new definition
     *
     * @param Definition $definition
     * @return DefinitionEntity
     * @throws RepositoryException
     */
    public function create(Definition $definition): DefinitionEntity;

    /**
     * Updates a definition
     *
     * @param DefinitionEntity $definition
     * @throws RepositoryException
     */
    public function update(DefinitionEntity $definition): void;

    /**
     * Removes a definition
     *
     * @param DefinitionEntity $definition
     * @throws RepositoryException
     */
    public function remove(DefinitionEntity $definition): void;
}
