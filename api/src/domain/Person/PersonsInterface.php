<?php

namespace Domain\Person;

interface PersonsInterface
{
    public function find(?int $limit, ?int $offset) : iterable;
    public function findOne() : PersonInterface;
    public function count() : int;
}
