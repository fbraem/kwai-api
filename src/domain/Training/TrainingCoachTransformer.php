<?php

namespace Domain\Training;

use League\Fractal;

class TrainingCoachTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'coaches';

    protected $defaultIncludes = [
        'member'
    ];

    public static function createForItem(Coach $item)
    {
        return new Fractal\Resource\Item($item, new self(), self::$type);
    }

    public static function createForCollection(iterable $collection)
    {
        return new Fractal\Resource\Collection($collection, new self(), self::$type);
    }

    public function includeMember(Coach $coach)
    {
        $member = $coach->member;
        if ($member) {
            //TODO; make it sport independent
            return \Judo\Domain\Member\MemberTransformer::createForItem($member);
        }
    }

    public function transform(Coach $coach)
    {
        $result = $coach->toArray();
        unset($result['_joinData']);
        $result['present'] = $coach['_joinData']['present'];
        $result['coach_type'] = $coach['_joinData']['coach_type'];
        $result['payed'] = $coach['_joinData']['payed'];
        $result['coach_remark'] = $coach['_joinData']['remark'];
        return $result;
    }
}
