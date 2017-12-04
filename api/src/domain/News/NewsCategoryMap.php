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

    public function news(NewsCategory $category)
    {
        return $this->hasMany($category, NewsStory::class, 'category_id', 'id');
    }

    public function author(NewsCategory $category)
    {
        return $this->belongsTo($category, \Domain\User\User::class, 'user_id', 'id');
    }
}
