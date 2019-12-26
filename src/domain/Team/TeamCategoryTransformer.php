<?php

namespace Domain\Team;

use League\Fractal;

class TeamCategoryTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'team_categories';

    public static function createForItem(TeamCategory $category)
    {
        return new Fractal\Resource\Item($category, new self(), self::$type);
    }

    public static function createForCollection(iterable $categories)
    {
        return new Fractal\Resource\Collection($categories, new self(), self::$type);
    }

    public function transform(TeamCategory $category)
    {
        return $category->toArray();
    }
}
