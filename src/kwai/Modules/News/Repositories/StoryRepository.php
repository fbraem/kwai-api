<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Repositories;

use Kwai\Core\Domain\Entity;
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
     * @return Entity
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
}
