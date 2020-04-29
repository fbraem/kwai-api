<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);


namespace Kwai\Modules\News\Repositories;

use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface ContentQuery
 *
 * Interface for querying news content.
 */
interface ContentQuery extends Query
{
    /**
     * Filter all news content for the given ids.
     *
     * @param int[] $ids
     */
    public function filterIds(array $ids): void;
}
