<?php

namespace Judo\Domain\Member;

use League\Fractal;

class MemberTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'person'
    ];

    public function transform(MemberInterface $member)
    {
        return $member->extract();
    }

    public function includePerson(MemberInterface $member)
    {
        $person = $member->person();
        if ($person) {
            return $this->item($person, new \Domain\Person\PersonTransformer, 'persons');
        }
    }
}
