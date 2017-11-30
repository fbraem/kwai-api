<?php
namespace Domain\Content;

use Analogue\ORM\EntityMap;

/**
 * @inheritdoc
 */
class ContentMap extends EntityMap
{
    protected $table = 'contents';

    public $timestamps = true;

    public function news(Content $content)
    {
        return $this->morphedByMany($content, \Domain\News\NewsStory::class, 'contentable');
    }
}
