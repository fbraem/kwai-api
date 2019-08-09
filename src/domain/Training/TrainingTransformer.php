<?php

namespace Domain\Training;

use League\Fractal;

use Domain\Team\TeamTransformer;

class TrainingTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'trainings';

    protected $defaultIncludes = [
        'season',
        'definition',
        'coaches',
        'teams',
        'presences'
    ];

    public static function createForItem(Training $training)
    {
        return new Fractal\Resource\Item($training, new self(), self::$type);
    }

    public static function createForCollection(iterable $trainings)
    {
        return new Fractal\Resource\Collection($trainings, new self(), self::$type);
    }

    public function includeSeason(Training $training)
    {
        $season = $training->season;
        if ($season) {
            return \Domain\Game\SeasonTransformer::createForItem($season);
        }
    }

    public function includeDefinition(Training $training)
    {
        $def = $training->definition;
        if ($def) {
            return DefinitionTransformer::createForItem($def);
        }
    }

    public function includeCoaches(Training $training)
    {
        $coaches = $training->coaches;
        if ($coaches) {
            return TrainingCoachTransformer::createForCollection($coaches);
        }
    }

    public function includePresences(Training $training)
    {
        $presences = $training->presences;
        if ($presences) {
            return PresenceTransformer::createForCollection($presences);
        }
    }

    public function includeTeams(Training $training)
    {
        $teams = $training->teams;
        if ($teams) {
            return TeamTransformer::createForCollection($teams);
        }
    }

    public function transform(Training $training)
    {
        $arr = $training->toArray();
        unset($arr['_matchingData']);
        if ($arr['event']) {
            unset($arr['event']['id']);
            if (is_array($arr['event']['contents'])) {
                foreach ($arr['event']['contents'] as &$content) {
                    unset($content['id']);
                    unset($content['_joinData']);
                }
            }
        }
        return $arr;
    }
}
