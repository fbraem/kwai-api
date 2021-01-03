<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Domain\Story;

/**
 * Interface StoryRepository
 */
interface StoryRepository
{
    /**
     * Get an overview with the number of news stories for year/month.
     * The array contains an object with year, month, count properties.
     *
     * @throws RepositoryException
     * @return array
     */
    public function getArchive(): array;

    /**
     * Get the story with the given id
     *
     * @param int $id
     * @return Entity<Story>
     * @throws RepositoryException
     * @throws StoryNotFoundException
     */
    public function getById(int $id): Entity;

    /**
     * Creates a query
     *
     * @return StoryQuery
     */
    public function createQuery(): StoryQuery;

    /**
     * Saves a new story
     * @param Story $story
     * @return Entity<Story>
     * @throws RepositoryException
     */
    public function create(Story $story): Entity;

    /**
     * Updates a story
     *
     * @param Entity<Story> $story
     * @throws RepositoryException
     */
    public function update(Entity $story): void;

    /**
     * Remove a story
     *
     * @param Entity $story
     * @throws RepositoryException
     */
    public function remove(Entity $story);

    /**
     * Get all stories using the given query.
     *
     * @param StoryQuery|null $query
     * @param int|null        $limit
     * @param int|null        $offset
     * @return Collection
     * @throws QueryException
     */
    public function getAll(
        ?StoryQuery $query = null,
        ?int $limit = null,
        ?int $offset = null): Collection;
}
