<?php

namespace Domain\Training;

use League\Fractal;

class PresenceTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'presences';

    protected $defaultIncludes = [
        'member'
    ];

    public static function createForItem(Presence $presence)
    {
        return new Fractal\Resource\Item($presence, new self(), self::$type);
    }

    public static function createForCollection(iterable $presences)
    {
        return new Fractal\Resource\Collection($presences, new self(), self::$type);
    }

    public function includeMember(Presence $presence)
    {
        $member = $presence->member;
        if ($member) {
            //TODO; make it sport independent
            return \Judo\Domain\Member\MemberTransformer::createForItem($member);
        }
    }

    public function transform(Presence $presence)
    {
        return $presence->toArray();
    }
}
