<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Exceptions\TrainingDefinitionNotFoundException;
use Kwai\Modules\Trainings\Domain\TrainingDefinition;

/**
 * Interface TrainingDefinitionRepository
 */
interface TrainingDefinitionRepository
{
    /**
     * Get the definition with the given id
     *
     * @throws RepositoryException
     * @throws TrainingDefinitionNotFoundException
     * @param int $id
     * @return Entity<TrainingDefinition>
     */
    public function getById(int $id): Entity;

    /**
     * Create a query
     *
     * @return TrainingDefinitionQuery
     */
    public function createQuery(): TrainingDefinitionQuery;
}
