<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Modules\Trainings\Domain\Exceptions\TeamNotFoundException;
use Kwai\Modules\Trainings\Domain\Team;

/**
 * Interface TeamRepository
 */
interface TeamRepository
{
    /**
     * Get the team with the given id.
     *
     * @param int $id
     * @return Entity<Team>
     * @throws QueryException
     * @throws TeamNotFoundException
     */
    public function getById(int $id): Entity;

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
