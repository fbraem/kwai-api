<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Club\Domain\Exceptions\TeamNotFoundException;
use Kwai\Modules\Club\Domain\Team;

/**
 * Interface TeamRepository
 */
interface TeamRepository
{
    /**
     * Get the team with the given id
     *
     * @param int $id
     * @return Entity<Team>
     * @throws RepositoryException
     * @throws TeamNotFoundException
     */
    public function getById(int $id): Entity;

    /**
     * Creates a query.
     *
     * @return TeamQuery
     */
    public function createQuery(): TeamQuery;

    /**
     * Executes the query and returns a collection with entities.
     *
     * @param TeamQuery|null $query
     * @param int|null       $limit
     * @param int|null       $offset
     * @return Collection
     * @throws RepositoryException
     */
    public function getAll(
        ?TeamQuery $query = null,
        ?int $limit = null,
        ?int $offset = null
    ): Collection;
}
