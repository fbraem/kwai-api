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
use Kwai\Modules\Trainings\Domain\Exceptions\CoachNotFoundException;
use Kwai\Modules\Trainings\Infrastructure\Mappers\CoachMapper;
use Kwai\Modules\Trainings\Repositories\CoachQuery;
use Kwai\Modules\Trainings\Repositories\CoachRepository;

/**
 * Class CoachDatabaseRepository
 *
 * Database repository for the Coach entity
 */
class CoachDatabaseRepository extends DatabaseRepository implements CoachRepository
{
    /**
     * @inheritDoc
     */
    public function createQuery(): CoachQuery
    {
        return new CoachDatabaseQuery($this->db);
    }

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

        if (count($entities) === 1) {
            return $entities[$id];
        }
        throw new CoachNotFoundException($id);
    }

    /**
     * @inheritDoc
     */
    public function getAll(CoachQuery $query, ?int $limit = null, ?int $offset = null): Collection
    {
        /* @var Collection $coaches */
        $coaches = $query->execute($limit, $offset);
        return $coaches->transform(
            fn($item) => CoachMapper::toDomain($item)
        );
    }
}
