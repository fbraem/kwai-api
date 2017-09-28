<?php

namespace Domain\News;

use League\Fractal;

class NewsStoryTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'category'
    ];

    public function transform(NewsStory $story)
    {
        return [
            'id' => (int) $story->id,
            'title' => $story->title,
            'summary' => $story->summary,
            'content' => $story->content,
            'publish_date' => $story->publish_date,
            'enabled' => $story->enabled,
            'featured' => $story->featured,
            'featured_end_date' => $story->featured_end_date,
            'end_date' => $story->end_date,
            'remark' => $story->remark,
            'created_at' => $story->created_at,
            'updated_at' => $story->updated_at
        ];
    }

    public function includeCategory(NewsStory $story)
    {
        $category = $story->category;
        return $this->item($category, new NewsCategoryTransformer, 'news_category');
    }
}
