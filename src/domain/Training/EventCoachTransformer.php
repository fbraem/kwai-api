<?php

namespace Domain\Training;

use League\Fractal;

class EventCoachTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'training_event_coaches';

    protected $defaultIncludes = [
        'training_coach'
    ];

    public static function createForItem(EventCoach $item)
    {
        return new Fractal\Resource\Item($item, new self(), self::$type);
    }

    public static function createForCollection(iterable $collection)
    {
        return new Fractal\Resource\Collection($collection, new self(), self::$type);
    }

    public function includeCoach(EventCoach $eventCoach)
    {
        $coach = $eventCoach->coach;
        if ($coach) {
            return CoachTransformer::createForItem($coach);
        }
    }

    public function transform(EventCoach $eventCoach)
    {
        return $eventCoach->toArray();
    }
}
