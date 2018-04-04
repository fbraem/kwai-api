<?php

namespace Judo\Domain\Member;

use League\Fractal;

class MemberTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'sport_judo_members';

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

    public function transform(Member $member)
    {
        $data = $member->toArray();
        unset($data['_joinData']);
        return $data;
    }

    public function includePerson(Member $member)
    {
        $person = $member->person;
        if ($person) {
            return \Domain\Person\PersonTransformer::createForItem($person);
        }
    }
}
