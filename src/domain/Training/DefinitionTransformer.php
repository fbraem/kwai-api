<?php

namespace Domain\Training;

use League\Fractal;

class DefinitionTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'definitions';

    protected $defaultIncludes = [
        'season',
        'team'
    ];

    public static function createForItem(Definition $def)
    {
        return new Fractal\Resource\Item($def, new self(), self::$type);
    }

    public static function createForCollection(iterable $defs)
    {
        return new Fractal\Resource\Collection($defs, new self(), self::$type);
    }

    public function includeSeason(Definition $def)
    {
        $season = $def->season;
        if ($season) {
            return \Domain\Game\SeasonTransformer::createForItem($season);
        }
    }

    public function includeTeam(Definition $def)
    {
        $team = $def->team;
        if ($team) {
            return \Domain\Team\TeamTransformer::createForItem($team);
        }
    }

    public function transform(Definition $def)
    {
        return $def->toArray();
    }
}
