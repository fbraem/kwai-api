<?php

namespace Domain\Category;

use League\Fractal;

class CategoryTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'categories';

    public static function createForItem(Category $category)
    {
        return new Fractal\Resource\Item($category, new self(), self::$type);
    }

    public static function createForCollection(iterable $categories)
    {
        return new Fractal\Resource\Collection($categories, new self(), self::$type);
    }

    public function transform(Category $category)
    {
        return $category->toArray();
    }
}
