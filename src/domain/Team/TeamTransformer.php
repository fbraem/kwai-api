<?php

namespace Domain\Team;

use League\Fractal;

class TeamTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'teams';

    protected $defaultIncludes = [
        'team_category',
        'season',
        'members'
    ];

    public static function createForItem(Team $team)
    {
        return new Fractal\Resource\Item($team, new self(), self::$type);
    }

    public static function createForCollection(iterable $teams)
    {
        return new Fractal\Resource\Collection($teams, new self(), self::$type);
    }

    public function includeTeamCategory(Team $team)
    {
        $category = $team->team_category;
        if ($category) {
            return TeamCategoryTransformer::createForItem($category);
        }
    }

    public function includeMembers(Team $team)
    {
        $members = $team->members;
        if ($members) {
            //TODO: Remove sport dependency?
            return \Judo\Domain\Member\MemberTransformer::createForCollection($members);
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
        $result = $team->toArray();
        unset($result['_joinData']);
        return $result;
    }
}
