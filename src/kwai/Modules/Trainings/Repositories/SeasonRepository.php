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
use Kwai\Modules\Trainings\Domain\Exceptions\SeasonNotFoundException;
use Kwai\Modules\Trainings\Domain\Season;

/**
 * Interface SeasonRepository
 */
interface SeasonRepository
{
    /**
     * Get the season with the given id.
     *
     * @param int $id
     * @return Entity<Season>
     * @throws QueryException
     * @throws SeasonNotFoundException
     */
    public function getById(int $id): Entity;

    /**
     * Get all seasons
     *
     * @param int|null $limit
     * @param int|null $offset
     * @return Collection
     * @throws QueryException
     */
    public function getAll(?int $limit = null, ?int $offset = null): Collection;
}
