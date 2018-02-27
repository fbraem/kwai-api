<?php

namespace Judo\Domain\Member;

interface MembersInterface
{
    public function find(?int $limit, ?int $offset) : iterable;
    public function findOne() : MemberInterface;
    public function count() : int;
}
