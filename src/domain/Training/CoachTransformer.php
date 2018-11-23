<?php

namespace Domain\Training;

use League\Fractal;

class CoachTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'training_coaches';

    protected $defaultIncludes = [
        'member'
    ];

    public static function createForItem(Coach $coach)
    {
        return new Fractal\Resource\Item($coach, new self(), self::$type);
    }

    public static function createForCollection(iterable $coaches)
    {
        return new Fractal\Resource\Collection($coaches, new self(), self::$type);
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
        return $coach->toArray();
    }
}
