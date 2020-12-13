<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\QueryException;

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
     * @throws QueryException
     */
    public function getById(int ... $ids): Collection;

    /**
     * Get all teams
     *
     * @param int|null $limit
     * @param int|null $offset
     * @return Collection
     * @throws QueryException
     */
    public function getAll(?int $limit = null, ?int $offset = null): Collection;
}
