<?php

namespace Domain\News;

use Analogue\ORM\Repository;

/**
 * Repository that handles read/writes for NewsCategory
 */
class NewsCategoryRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(NewsCategory::class);
    }

    public function count()
    {
        return $this->mapper->count();
    }
}
