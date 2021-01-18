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
    public function getById(int ...$ids): Collection
    {
        $query = $this->createQuery();
        $query->filterId(...$ids);

        try {
            $coaches = $this->getAll($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        return $coaches;
    }

    /**
     * @inheritDoc
     */
    public function getAll(?CoachQuery $query = null, ?int $limit = null, ?int $offset = null): Collection
    {
        $query ??= $this->createQuery();

        /* @var Collection $coaches */
        $coaches = $query->execute($limit, $offset);
        return $coaches->mapWithKeys(
            fn($item) => [
                $item['id'] => new Entity(
                    (int) $item['id'],
                    CoachMapper::toDomain($item)
                )
            ]
        );
    }
}
