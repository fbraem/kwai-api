<?php

namespace Domain\News;

use Analogue\ORM\Repository;

/**
 * Repository that handles read/writes for NewsStory
 */
class NewsStoryRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(NewsStory::class);
    }

    public function count()
    {
        return $this->mapper->count();
    }
}
