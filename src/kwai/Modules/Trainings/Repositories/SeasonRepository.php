<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;

/**
 * Interface SeasonRepository
 */
interface SeasonRepository
{
    /**
     * Get the season with the given id(s).
     *
     * @param int ...$id
     * @return Collection
     * @throws RepositoryException
     */
    public function getById(int ...$id): Collection;

    /**
     * Get all seasons
     *
     * @param SeasonQuery|null $query
     * @param int|null         $limit
     * @param int|null         $offset
     * @return Collection
     * @throws RepositoryException
     */
    public function getAll(
        ?SeasonQuery $query = null,
        ?int $limit = null,
        ?int $offset = null
    ): Collection;
}
