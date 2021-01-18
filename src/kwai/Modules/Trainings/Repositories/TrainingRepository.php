<?php
/**
 * @package Modules
 * @subpackage Training
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Training;
use Kwai\Modules\Trainings\Domain\Exceptions\TrainingNotFoundException;

/**
 * Interface TrainingRepository
 */
interface TrainingRepository
{
    /**
     * Get the training with the given id.
     *
     * @throws RepositoryException
     * @throws TrainingNotFoundException
     * @param int $id
     * @return Entity<Training>
     */
    public function getById(int $id): Entity;

    /**
     * Creates a query
     *
     * @return TrainingQuery
     */
    public function createQuery(): TrainingQuery;

    /**
     * Executes the query and returns a Collection with entities.
     * For each record, the mapper will be called to create an entity.
     *
     * @param TrainingQuery|null $query
     * @param int|null           $limit
     * @param int|null           $offset
     * @return Collection
     * @throws QueryException
     */
    public function getAll(?TrainingQuery $query, ?int $limit, ?int $offset): Collection;

    /**
     * Save a new training to the database.
     *
     * @param Training $training
     * @return Entity<Training>
     * @throws RepositoryException
     */
    public function create(Training $training): Entity;

    /**
     * Updates a training
     *
     * @param Entity $training
     * @throws RepositoryException
     */
    public function update(Entity $training): void;
}
