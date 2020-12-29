<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Repositories;

use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface CoachQuery
 *
 * Interface for querying coaches
 */
interface CoachQuery extends Query
{
    /**
     * Add a filter for the given ids
     *
     * @param int ...$id
     * @return CoachQuery
     */
    public function filterIds(int ...$id): self;

    /**
     * Add a filter for the (non-)active coaches
     *
     * @param bool $active
     * @return CoachQuery
     */
    public function filterActive(bool $active): self;

    /**
     * Add a filter for the associated member
     *
     * @param int $memberId
     * @return CoachQuery
     */
    public function filterMember(int $memberId): self;
}
