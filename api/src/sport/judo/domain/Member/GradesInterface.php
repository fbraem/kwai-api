<?php

namespace Judo\Domain\Member;

interface GradesInterface
{
    public function find(?int $limit, ?int $offset) : iterable;
    public function findOne() : GradeInterface;
    public function count() : int;
}
