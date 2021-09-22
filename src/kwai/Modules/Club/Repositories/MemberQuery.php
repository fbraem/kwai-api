<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Repositories;

use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface MemberQuery
 */
interface MemberQuery extends Query
{
    /**
     * Add a filter for the given id
     *
     * @param int $id
     * @return self
     */
    public function filterId(int $id): self;
}
