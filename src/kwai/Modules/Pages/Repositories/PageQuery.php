<?php
/**
 * @package Pages
 * @subpackage Repositories
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Repositories;

use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface PageQuery
 *
 * Interface for querying pages.
 */
interface PageQuery extends Query
{
    /**
     * Add a filter for the given id
     *
     * @param int $id
     */
    public function filterId(int $id): void;

    /**
     * Add a filter on the application
     *
     * @param int $id
     */
    public function filterApplication(int $id): void;

    /**
     * Add a filter to query only the enabled pages.
     */
    public function filterVisible(): void;

    /**
     * Filter the pages only for this user.
     *
     * @param int $id
     */
    public function filterUser(int $id): void;
}
