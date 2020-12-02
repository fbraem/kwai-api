<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Repositories;

use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface TrainingDefinitionQuery
 *
 * Interface for querying training definitions.
 */
interface TrainingDefinitionQuery extends Query
{
    /**
     * Add a filter for the given id
     *
     * @param int $id
     */
    public function filterId(int $id): void;
}
