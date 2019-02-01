<?php

namespace Domain\Training;

use League\Fractal;

class TrainingTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'trainings';

    protected $defaultIncludes = [
        'season',
        'definition',
        'event'
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

    public function includeEvent(Training $training)
    {
        $event = $training->event;
        if ($event) {
            return \Domain\Event\EventTransformer::createForItem($event);
        }
    }

    public function includeDefinition(Training $training)
    {
        $def = $training->definition;
        if ($def) {
            return DefinitionTransformer::createForItem($def);
        }
    }

    public function transform(Training $training)
    {
        return $training->toArray();
    }
}
