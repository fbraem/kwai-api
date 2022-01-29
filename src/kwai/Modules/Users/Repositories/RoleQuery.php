<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface RoleQuery
 */
interface RoleQuery extends Query
{
    /**
     * Filter by id of the role
     *
     * @param int ...$id
     * @return $this
     */
    public function filterByIds(int ...$id): self;
}
