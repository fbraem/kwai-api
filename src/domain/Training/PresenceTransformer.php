<?php

namespace Domain\Training;

use League\Fractal;

//TODO; make it sport independent
use Judo\Domain\Member\Member;

class PresenceTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'presences';

    protected $defaultIncludes = [
        'person'
    ];

    public static function createForItem(Member $member)
    {
        return new Fractal\Resource\Item($member, new self(), self::$type);
    }

    public static function createForCollection(iterable $members)
    {
        return new Fractal\Resource\Collection($members, new self(), self::$type);
    }

    public function includePerson(Member $member)
    {
        $person = $member->person;
        if ($person) {
            return \Domain\Person\PersonTransformer::createForItem($person);
        }
    }

    public function transform(Member $member)
    {
        $result = $member->toArray();
        unset($result['_joinData']);
        $result['presence_remark'] = $member['_joinData']['remark'];
        return $result;
    }
}
