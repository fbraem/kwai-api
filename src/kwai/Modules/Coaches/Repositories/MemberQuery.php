<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Repositories;

use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface MemberQuery
 *
 * Interface for querying members.
 */
interface MemberQuery extends Query
{
    /**
     * Add a filter for the given id
     *
     * @param int $id
     * @return MemberQuery
     */
    public function filterId(int $id): self;
}
