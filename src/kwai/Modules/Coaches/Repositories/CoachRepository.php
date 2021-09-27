<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Coaches\Domain\Exceptions\CoachNotFoundException;
use Kwai\Modules\Coaches\Domain\Coach;

/**
 * Interface CoachRepository
 */
interface CoachRepository
{
    /**
     * Get the coach with the given id
     *
     * @param int $id
     * @return Entity<Coach>
     * @throws RepositoryException
     * @throws CoachNotFoundException
     */
    public function getById(int $id): Entity;

    /**
     * Creates a query
     *
     * @return CoachQuery
     */
    public function createQuery(): CoachQuery;

    /**
     * Executes the query and returns a collection with entities
     *
     * @param CoachQuery|null $query
     * @param int|null        $limit
     * @param int|null        $offset
     * @return Collection
     * @throws RepositoryException
     */
    public function getAll(
        ?CoachQuery $query = null,
        ?int $limit = null,
        ?int $offset = null
    ): Collection;

    /**
     * Update a coach.
     *
     * @param Entity<Coach> $coach
     * @throws RepositoryException
     */
    public function update(Entity $coach): void;

    /**
     * Create a coach.
     *
     * @param Coach $coach
     * @return Entity<Coach>
     * @throws RepositoryException
     */
    public function create(Coach $coach): Entity;
}
