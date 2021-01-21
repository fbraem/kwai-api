<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;

/**
 * Interface TeamRepository
 */
interface TeamRepository
{
    /**
     * Get the team with the given id.
     *
     * @param int ...$ids
     * @return Collection
     * @throws RepositoryException
     */
    public function getById(int ...$ids): Collection;

    /**
     * Get all teams
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
