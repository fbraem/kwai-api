<?php

namespace Judo\Domain\Member;

use League\Fractal;

use Domain\Person\PersonTransformer;
use Domain\Team\TeamTransformer;

class MemberTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'members';

    protected $defaultIncludes = [
        'person',
        'trainings',
        'teams'
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
        if (isset($member->person)) {
            return PersonTransformer::createForItem($member->person);
        }
    }

    public function includeTeams(Member $member)
    {
        if (isset($member->teams)) {
            return TeamTransformer::createForCollection($member->teams);
        }
    }

    public function includeTrainings(Member $member)
    {
        if (isset($member->trainings)) {
            return TrainingParticipationsTransformer::createForCollection($member->trainings);
        }
    }
}
