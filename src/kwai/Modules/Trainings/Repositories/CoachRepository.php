<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\Modules\Trainings\Domain\Exceptions\CoachNotFoundException;

/**
 * Interface CoachRepository
 */
interface CoachRepository
{
    /**
     * Get the coach with the given id.
     *
     * @throws RepositoryException
     * @throws CoachNotFoundException
     * @param int $id
     * @return Entity<Coach>
     */
    public function getById(int $id): Entity;

    /**
     * Creates a query
     *
     * @return CoachQuery
     */
    public function createQuery(): CoachQuery;

    /**
     * Executes the query and returns a Collection with entities.
     * For each record, the mapper will be called to create an entity.
     *
     * @param CoachQuery $query
     * @param int|null   $limit
     * @param int|null   $offset
     * @return Collection
     * @throws QueryException
     */
    public function execute(CoachQuery $query, ?int $limit, ?int $offset): Collection;
}
