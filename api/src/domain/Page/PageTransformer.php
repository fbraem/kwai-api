<?php

namespace Domain\Page;

use League\Fractal;

class PageTransformer extends Fractal\TransformerAbstract
{
    private $filesystem;

    protected $defaultIncludes = [
        'contents',
        'category'
    ];

    public function __construct($filesystem = null)
    {
        $this->filesystem = $filesystem;
    }

    public function transform(PageInterface $page)
    {
        $result = $page->extract();

        if ($this->filesystem) {
            $images = $this->filesystem->listContents('images/pages/' . $page->id());
            foreach ($images as $image) {
                $result[$image['filename']] = '/files/' . $image['path'];
            }
        }
        return $result;
    }

    public function includeContents(Page $page)
    {
        $contents = $page->contents();
        if ($contents) {
            return $this->collection($contents->contents(), new \Domain\Content\ContentTransformer, 'contents');
        }
    }

    public function includeCategory(Page $page)
    {
        $category = $page->category();
        if ($category) {
            return $this->item($category, new \Domain\Category\CategoryTransformer, 'categories');
        }
    }
}
