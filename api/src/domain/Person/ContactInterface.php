<?php

namespace Domain\Person;

interface ContactInterface extends \Domain\DatetimeMetaInterface, \Domain\HydratorInterface
{
    public function id() : ?int;
    public function email() : ?string;
    public function tel() : ?string;
    public function mobile() : ?string;
    public function address() : ?string;
    public function postal_code() : ?string;
    public function city() : ?string;
    public function county() : ?string;
    public function country() : ?\Domain\Person\CountryInterface;
    public function remark() : ?string;

    public function store();
    public function delete();
}
