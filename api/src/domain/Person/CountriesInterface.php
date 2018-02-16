<?php

namespace Domain\Person;

interface CountriesInterface
{
    public function find(?int $limit, ?int $offset) : iterable;
    public function findOne() : CountryInterface;
    public function count() : int;
}
