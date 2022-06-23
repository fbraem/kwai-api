<?php
/**
 * @package Modules
 * @subpackage Training
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Training;
use Kwai\Modules\Trainings\Domain\Exceptions\TrainingNotFoundException;
use Kwai\Modules\Trainings\Domain\TrainingEntity;

/**
 * Interface TrainingRepository
 */
interface TrainingRepository
{
    /**
     * Get the training with the given id.
     *
     * @param int $id
     * @return TrainingEntity
     * @throws RepositoryException
     * @throws TrainingNotFoundException
     */
    public function getById(int $id): TrainingEntity;

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
     * @throws RepositoryException
     */
    public function getAll(
        ?TrainingQuery $query = null,
        ?int $limit = null,
        ?int $offset = null
    ): Collection;

    /**
     * Save a new training to the database.
     *
     * @param Training $training
     * @return TrainingEntity
     * @throws RepositoryException
     */
    public function create(Training $training): TrainingEntity;

    /**
     * Updates a training
     *
     * @param TrainingEntity $training
     * @throws RepositoryException
     */
    public function update(TrainingEntity $training): void;
}
