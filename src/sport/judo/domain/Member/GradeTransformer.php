<?php

namespace Judo\Domain\Member;

use League\Fractal;

class GradeTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'sport_judo_grades';

    public static function createForItem(Grade $grade)
    {
        return new Fractal\Resource\Item($grade, new self(), self::$type);
    }

    public static function createForCollection(iterable $grades)
    {
        return new Fractal\Resource\Collection($grades, new self(), self::$type);
    }

    public function transform(Grade $grade)
    {
        return $grade->toArray();
    }
}
