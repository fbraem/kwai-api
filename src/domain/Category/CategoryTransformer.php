<?php

namespace Domain\Category;

use League\Fractal;
use League\Flysystem\Filesystem;

class CategoryTransformer extends Fractal\TransformerAbstract
{
    private $filesystem;

    private static $type = 'categories';

    public function __construct(Filesystem $filesystem = null)
    {
        $this->filesystem = $filesystem;
    }

    public static function createForItem(Category $category, Filesystem $filesystem = null)
    {
        return new Fractal\Resource\Item($category, new self($filesystem), self::$type);
    }

    public static function createForCollection(iterable $categories, Filesystem $filesystem = null)
    {
        return new Fractal\Resource\Collection($categories, new self($filesystem), self::$type);
    }

    public function transform(Category $category)
    {
        $result = $category->toArray();
        if ($this->filesystem) {
            $images = $this->filesystem->listContents('images/categories/' . $category->id);
            if (count($images) > 0) {
                $result['images'] = [];
                foreach ($images as $image) {
                    $result['images'][$image['filename']] = '/files/' . $image['path'];
                }
            }
        }
        return $result;
    }
}
