<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Repositories;

use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface UserQuery
 */
interface UserQuery extends Query
{
    /**
     * Add a filter for the given id
     *
     * @param int $id
     */
    public function filterId(int $id): self;
}
