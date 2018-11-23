<?php

namespace Domain\Training;

use League\Fractal;

class EventTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'training_events';

    protected $defaultIncludes = [
        'season',
        'training_definition'
    ];

    public static function createForItem(Event $event)
    {
        return new Fractal\Resource\Item($event, new self(), self::$type);
    }

    public static function createForCollection(iterable $events)
    {
        return new Fractal\Resource\Collection($events, new self(), self::$type);
    }

    public function includeSeason(Event $event)
    {
        $season = $event->season;
        if ($season) {
            return \Domain\Game\SeasonTransformer::createForItem($season);
        }
    }

    public function includeTrainingDefinition(Event $event)
    {
        $def = $event->training_definition;
        if ($def) {
            return DefinitionTransformer::createForItem($def);
        }
    }

    public function transform(Event $event)
    {
        return $event->toArray();
    }
}
