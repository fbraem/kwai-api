<?php
namespace Domain\News;

use Analogue\ORM\EntityMap;

/**
 * @inheritdoc
 */
class NewsCategoryMap extends EntityMap
{
    protected $table = 'news_categories';

    public $timestamps = true;

    public function news(NewsStory $news)
    {
        return $this->hasMany($news, NewsStory::class, 'category_id', 'id');
    }
}
