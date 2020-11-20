<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Repositories;

use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface CoachQuery
 *
 * Interface for querying coaches.
 */
interface CoachQuery extends Query
{
    /**
     * Add a filter for the given id
     *
     * @param int $id
     */
    public function filterId(int $id): void;

    /**
     * Add a filter for active/non-active coaches.
     *
     * @param bool $active
     */
    public function filterActive(bool $active): void;
}
