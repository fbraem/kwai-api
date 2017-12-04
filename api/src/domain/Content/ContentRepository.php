<?php

namespace Domain\Content;

use Analogue\ORM\Repository;

/**
 * Repository that handles read/writes for NewsStory
 */
class ContentRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(Content::class);
    }
}
