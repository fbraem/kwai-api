<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface UserRoleQuery
 */
interface UserRoleQuery extends Query
{
    public function filterByUser(int ...$userIds): self;
}
