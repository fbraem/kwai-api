<?php

namespace Domain\Person;

interface CountryInterface extends \Domain\DatetimeMetaInterface, \Domain\HydratorInterface
{
    public function id() : ?int;
    public function iso_2() : ?string;
    public function iso_3() : ?string;
    public function name() : ?string;

    public function store();
    public function delete();
}
