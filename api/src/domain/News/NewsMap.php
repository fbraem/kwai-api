<?php
namespace Domain\News;

use Analogue\ORM\EntityMap;

/**
 * @inheritdoc
 */
class NewsMap extends EntityMap
{
    protected $table = 'news';

    public $timestamps = true;
}
