<?php

namespace Domain\Game;

use League\Fractal;

class SeasonTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'seasons';

    protected $defaultIncludes = [
        'teams'
    ];

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

    public function includeTeams(Season $season)
    {
        $teams = $seasons->teams;
        if ($teams) {
            return \Domain\Team\TeamTransformer::createForCollection($teams);
        }
    }
}
