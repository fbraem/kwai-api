<?php

namespace Domain\Team;

use League\Fractal;

class TeamTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'teams';

    protected $defaultIncludes = [
        'team_type',
        'season'
    ];

    public static function createForItem(Team $team)
    {
        return new Fractal\Resource\Item($team, new self(), self::$type);
    }

    public static function createForCollection(iterable $teams)
    {
        return new Fractal\Resource\Collection($teams, new self(), self::$type);
    }

    public function includeTeamType(Team $team)
    {
        $type = $team->team_type;
        if ($type) {
            return TeamTypeTransformer::createForItem($type);
        }
    }

    public function includeSeason(Team $team)
    {
        $season = $team->season;
        if ($season) {
            return \Domain\Game\SeasonTransformer::createForItem($season);
        }
    }

    public function transform(Team $team)
    {
        return $team->toArray();
    }
}
