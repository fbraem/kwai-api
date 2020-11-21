<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);


namespace Kwai\Modules\Trainings\Repositories;

use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface ContentQuery
 *
 * Interface for querying training content (ie text).
 */
interface ContentQuery extends Query
{
    /**
     * Filter all training content for the given ids.
     *
     * @param int[] $ids
     */
    public function filterIds(array $ids): void;

    /**
     * Filter all training content written by this user.
     *
     * @param int $id
     */
    public function filterCreator(int $id): void;
}
