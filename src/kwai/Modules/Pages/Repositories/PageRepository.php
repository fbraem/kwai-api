<?php
/**
 * @package Pages
 * @subpackage Repositories
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Pages\Domain\Exceptions\PageNotFoundException;
use Kwai\Modules\Pages\Domain\Page;

/**
 * Interface PageRepository
 */
interface PageRepository
{
    /**
     * Get the page with the given id
     *
     * @throws RepositoryException
     * @throws PageNotFoundException
     * @param int $id
     * @return Entity<Page>
     */
    public function getById(int $id): Entity;

    /**
     * Creates a query
     *
     * @return PageQuery
     */
    public function createQuery(): PageQuery;

    /**
     * Saves a new page
     *
     * @throws RepositoryException
     * @param Page $page
     * @return Entity<Page>
     */
    public function create(Page $page): Entity;

    /**
     * Updates a page
     *
     * @throws RepositoryException
     * @param Entity<Page> $page
     */
    public function update(Entity $page): void;

    /**
     * Removes a page
     *
     * @throws RepositoryException
     * @param Entity<Page> $page
     */
    public function remove(Entity $page): void;

    /**
     * Get all pages using the given query.
     *
     * @param PageQuery|null $query
     * @param int|null       $limit
     * @param int|null       $offset
     * @return Collection
     * @throws RepositoryException
     */
    public function getAll(?PageQuery $query = null, ?int $limit = null, ?int $offset = null): Collection;
}
