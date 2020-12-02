<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Exceptions\TrainingDefinitionNotFoundException;
use Kwai\Modules\Trainings\Repositories\TrainingDefinitionQuery;
use Kwai\Modules\Trainings\Repositories\TrainingDefinitionRepository;

/**
 * Class TrainingDefinitionDatabaseRepository
 */
class TrainingDefinitionDatabaseRepository extends DatabaseRepository implements TrainingDefinitionRepository
{
    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        $query = $this->createQuery();
        $query->filterId($id);

        try {
            $entities = $query->execute();
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        if ($entities->count() > 0) {
            return $entities->get($id);
        }

        throw new TrainingDefinitionNotFoundException($id);
    }

    /**
     * @inheritDoc
     */
    public function createQuery(): TrainingDefinitionQuery
    {
        return new TrainingDefinitionDatabaseQuery($this->db);
    }
}
