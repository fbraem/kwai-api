<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Exceptions\TrainingNotFoundException as TrainingNotFoundExceptionAlias;
use Kwai\Modules\Trainings\Infrastructure\Mappers\TrainingMapper;
use Kwai\Modules\Trainings\Repositories\TrainingQuery;
use Kwai\Modules\Trainings\Repositories\TrainingRepository;

/**
 * Class TrainingDatabaseRepository
 *
 * Repository for the Training entity.
 */
class TrainingDatabaseRepository extends DatabaseRepository implements TrainingRepository
{
    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        $query = $this->createQuery();
        $query->filterId($id);

        try {
            $entities = $this->getAll($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        if ($entities->count() === 1) {
            return $entities[$id];
        }

        throw new TrainingNotFoundExceptionAlias($id);
    }

    /**
     * @inheritDoc
     */
    public function createQuery(): TrainingQuery
    {
        return new TrainingDatabaseQuery($this->db);
    }

    /**
     * @inheritDoc
     */
    public function getAll(
        TrainingQuery $query,
        ?int $limit = null,
        ?int $offset = null
    ): Collection {
        /* @var Collection $trainings */
        $trainings = $query->execute($limit, $offset);
        return $trainings->mapWithKeys(
            fn($item, $key) => [ $key => TrainingMapper::toDomain($item) ]
        );
    }
}
