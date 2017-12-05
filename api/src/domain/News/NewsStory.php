<?php
namespace Domain\News;

use Analogue\ORM\Entity;
use Analogue\ORM\EntityCollection;

/**
 * @inheritdoc
 */
class NewsStory extends Entity
{
    public function __construct()
    {
        $this->contents = new EntityCollection();
    }
}
