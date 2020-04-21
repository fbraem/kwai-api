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
     * Get the story with the given id
     *
     * @param int $id
     * @return Entity<Story>
     * @throws RepositoryException
     * @throws StoryNotFoundException
     */
    public function getById(int $id): Entity;
}
