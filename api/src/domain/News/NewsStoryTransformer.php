<?php

namespace Domain\News;

use League\Fractal;

class NewsStoryTransformer extends Fractal\TransformerAbstract
{
    private $filesystem;

    protected $defaultIncludes = [
        'category',
        'author'
    ];

    public function __construct($filesystem = null)
    {
        $this->filesystem = $filesystem;
    }

    public function transform(NewsStory $story)
    {
        $result = [
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

        if ($this->filesystem) {
            $images = $this->filesystem->listContents('images/news/' . $story->id);
            foreach($images as $image) {
                $result[$image['filename']] = '/files/' . $image['path'];
            }
        }
        return $result;
    }

    public function includeCategory(NewsStory $story)
    {
        $category = $story->category;
        return $this->item($category, new NewsCategoryTransformer, 'news_category');
    }

    public function includeAuthor(NewsStory $story)
    {
        $author = $story->author;
        return $this->item($author, new \Domain\User\UserTransformer, 'user');
    }
}
