<?php

namespace Domain\Person;

interface ContactsInterface
{
    public function find(?int $limit, ?int $offset) : iterable;
    public function findOne() : ContractInterface;
    public function count() : int;
}
