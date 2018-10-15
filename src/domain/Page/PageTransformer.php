<?php

namespace Domain\Page;

use League\Fractal;

class PageTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'pages';

    private $filesystem;

    protected $defaultIncludes = [
        'contents',
        'category'
    ];

    public static function createForItem(Page $page, $filesystem)
    {
        return new Fractal\Resource\Item($page, new self($filesystem), self::$type);
    }

    public static function createForCollection(iterable $pages, $filesystem)
    {
        return new Fractal\Resource\Collection($pages, new self($filesystem), self::$type);
    }

    public function __construct($filesystem = null)
    {
        $this->filesystem = $filesystem;
    }

    public function transform(Page $page)
    {
        $result = $page->toArray();
        unset($result['_matchingData']);

        if ($this->filesystem) {
            $images = $this->filesystem->listContents('images/pages/' . $page->id);
            if (count($images) > 0) {
                $result['images'] = [];
                foreach ($images as $image) {
                    $result['images'][$image['filename']] = '/files/' . $image['path'];
                }
            }
        }
        return $result;
    }

    public function includeContents(Page $page)
    {
        $contents = $page->contents;
        if ($contents) {
            return \Domain\Content\ContentTransformer::createForCollection($contents);
        }
    }

    public function includeCategory(Page $page)
    {
        $category = $page->category;
        if ($category) {
            return \Domain\Category\CategoryTransformer::createForItem($category);
        }
    }
}
