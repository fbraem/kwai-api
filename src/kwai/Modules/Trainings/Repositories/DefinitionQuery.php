<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface DefinitionQuery
 *
 * Interface for querying training definitions.
 */
interface DefinitionQuery extends Query
{
    /**
     * Add a filter for the given id
     *
     * @param int $id
     * @return DefinitionQuery
     */
    public function filterId(int $id): self;

    /**
     * Add a filter to select all definitions with the given ids
     *
     * @param Collection $ids
     * @return DefinitionQuery
     */
    public function filterIds(Collection $ids): self;
}
