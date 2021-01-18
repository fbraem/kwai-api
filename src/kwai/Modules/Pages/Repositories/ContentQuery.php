<?php
/**
 * @package Pages
 * @subpackage Repositories
 */
declare(strict_types=1);


namespace Kwai\Modules\Pages\Repositories;

use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface ContentQuery
 *
 * Interface for querying page content.
 */
interface ContentQuery extends Query
{
    /**
     * Filter all page content for the given ids.
     *
     * @param int ...$ids
     * @return ContentQuery
     */
    public function filterIds(int ...$ids): self;

    /**
     * Filter all page content written by this user.
     *
     * @param int $id
     * @return ContentQuery
     */
    public function filterUser(int $id): self;
}
