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
use Kwai\Modules\Trainings\Domain\Exceptions\TrainingDefinitionNotFoundException;
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
     * @throws TrainingDefinitionNotFoundException
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
}
