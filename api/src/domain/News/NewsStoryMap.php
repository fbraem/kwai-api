<?php
namespace Domain\News;

use Analogue\ORM\EntityMap;

/**
 * @inheritdoc
 */
class NewsStoryMap extends EntityMap
{
    protected $table = 'news_stories';

    public $timestamps = true;

    public function category(NewsStory $news)
    {
        return $this->belongsTo($news, NewsCategory::class, 'category_id', 'id');
    }

    public function author(NewsStory $news)
    {
        return $this->belongsTo($news, \Domain\User\User::class, 'user_id', 'id');
    }
}
