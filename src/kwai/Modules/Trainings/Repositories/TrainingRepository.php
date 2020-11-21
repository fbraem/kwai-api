<?php
/**
 * @package Modules
 * @subpackage Training
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Repositories;

use Kwai\Core\Domain\Entity;
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
}
