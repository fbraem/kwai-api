<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Repositories\StoryRepository;

/**
 * Class StoryDatabaseRepository
 */
class StoryDatabaseRepository extends DatabaseRepository implements StoryRepository
{
    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        $query = $this->createQuery();
        $query->filterId($id);

        $entities = $query->execute();
        if (count($entities) == 1) {
            return $entities[$id];
        }
        throw new StoryNotFoundException($id);
    }

    /**
     * @inheritDoc
     */
    public function createQuery(): StoryDatabaseQuery
    {
        return new StoryDatabaseQuery($this->db);
    }
}
