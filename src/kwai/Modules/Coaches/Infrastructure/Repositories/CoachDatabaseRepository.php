<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Coaches\Domain\Exceptions\CoachNotFoundException;
use Kwai\Modules\Coaches\Infrastructure\Mappers\CoachMapper;
use Kwai\Modules\Coaches\Repositories\CoachQuery;
use Kwai\Modules\Coaches\Repositories\CoachRepository;

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
        $query->filterIds($id);

        try {
            $coaches = $this->getAll($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        if ($coaches->count() == 0) {
            throw new CoachNotFoundException($id);
        }

        return $coaches->first();
    }

    /**
     * @inheritDoc
     */
    public function getAll(CoachQuery $query, ?int $limit = null, ?int $offset = null): Collection
    {
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
