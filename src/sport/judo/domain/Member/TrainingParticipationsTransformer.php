<?php

namespace Judo\Domain\Member;

use League\Fractal;

use Domain\Training\Training;

class TrainingParticipationsTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'training_participations';

    public static function createForItem(Training $training)
    {
        return new Fractal\Resource\Item($training, new self(), self::$type);
    }

    public static function createForCollection(iterable $trainings)
    {
        return new Fractal\Resource\Collection($trainings, new self(), self::$type);
    }

    public function transform($obj)
    {
        $result = $obj->toArray();
        unset($result['_joinData']);
        $result['presence'] = [
            'remark' => $obj['_joinData']['remark']
        ];
        if ($result['event']) {
            unset($result['event']['id']);
        }
        return $result;
    }
}
