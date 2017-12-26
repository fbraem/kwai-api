<?php

namespace Domain\News;

use League\Fractal;

class NewsStoryTransformer extends Fractal\TransformerAbstract
{
    private $filesystem;

    protected $defaultIncludes = [
        'contents',
        'category',
        'author',
    ];

    public function __construct($filesystem = null)
    {
        $this->filesystem = $filesystem;
    }

    public function transform(NewsStoryInterface $story)
    {
        $result = $story->extract();

        $publishDate = new \Carbon\Carbon($result['publish_date'], 'UTC');
        //$publishDate->timezone($result['publish_date_timezone']);
        $result['publish_date'] = $publishDate->toDateTimeString();
        $result['created_at'] = (new \Carbon\Carbon($result['created_at']))->toIso8601String();

        if ($this->filesystem) {
            $images = $this->filesystem->listContents('images/news/' . $story->id());
            foreach ($images as $image) {
                $result[$image['filename']] = '/files/' . $image['path'];
            }
        }
        return $result;
    }

    public function includeContents(NewsStory $story)
    {
        $contents = $story->contents();
        if ($contents) {
            return $this->collection($contents->contents(), new \Domain\Content\ContentTransformer, 'contents');
        }
    }

    public function includeCategory(NewsStory $story)
    {
        $category = $story->category();
        if ($category) {
            return $this->item($category, new NewsCategoryTransformer, 'news_categories');
        }
    }

    public function includeAuthor(NewsStory $story)
    {
        $author = $story->author();
        if ($author) {
            return $this->item($author, new \Domain\User\UserTransformer, 'users');
        }
    }
}
