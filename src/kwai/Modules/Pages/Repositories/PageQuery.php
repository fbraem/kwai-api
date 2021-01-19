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
     * @return PageQuery
     */
    public function filterId(int $id): self;

    /**
     * Add a filter on the application
     *
     * @param string|int $nameOrId
     * @return PageQuery
     */
    public function filterApplication($nameOrId): self;

    /**
     * Add a filter to query only the enabled pages.
     *
     * @return PageQuery
     */
    public function filterVisible(): self;

    /**
     * Filter the pages only for this user.
     *
     * @param int $id
     * @return PageQuery
     */
    public function filterUser(int $id): self;

    /**
     * Order by priority
     * @return PageQuery
     */
    public function orderByPriority(): self;

    /**
     * Order by application title
     * @return PageQuery
     */
    public function orderByApplication(): self;

    /**
     * Order by creation date
     * @return PageQuery
     */
    public function orderByCreationDate(): self;
}
