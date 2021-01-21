<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Repositories;

use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface TeamQuery
 */
interface TeamQuery extends Query
{
    /**
     * Filter for the given id(s)
     *
     * @param int ...$id
     * @return TeamQuery
     */
    public function filterId(int ...$id): self;
}
