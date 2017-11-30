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

    public function category(NewsStory $story)
    {
        return $this->belongsTo($story, NewsCategory::class, 'category_id', 'id');
    }

    public function author(NewsStory $story)
    {
        return $this->belongsTo($story, \Domain\User\User::class, 'user_id', 'id');
    }

    public function contents(NewsStory $story)
    {
        return $this->morphToMany($story, \Domain\Content\Content::class, 'contentable');
    }
}
