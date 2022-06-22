<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\CoachEntity;

/**
 * Interface CoachRepository
 */
interface CoachRepository
{
    /**
     * Get the coaches with the given ids.
     *
     * @param int ...$id
     * @return Collection<CoachEntity>
     * @throws RepositoryException
     */
    public function getById(int ...$id): Collection;

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
     * @param CoachQuery|null $query
     * @param int|null        $limit
     * @param int|null        $offset
     * @return Collection<CoachEntity>
     * @throws RepositoryException
     */
    public function getAll(?CoachQuery $query = null, ?int $limit = null, ?int $offset = null): Collection;
}
