<?php

namespace Domain\Game;

use League\Fractal;

class SeasonTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'seasons';

    public static function createForItem(Season $season)
    {
        return new Fractal\Resource\Item($season, new self(), self::$type);
    }

    public static function createForCollection(iterable $seasons)
    {
        return new Fractal\Resource\Collection($seasons, new self(), self::$type);
    }

    public function transform(Season $season)
    {
        return $season->toArray();
    }
}
