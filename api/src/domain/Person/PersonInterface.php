<?php

namespace Domain\Person;

interface PersonInterface extends \Domain\DatetimeMetaInterface, \Domain\HydratorInterface
{
    public function id() : ?int;
    public function lastname() : ?string;
    public function firstname() : ?string;
    public function gender() : ?int;
    public function active() : boolean;
    public function birthdate() : ?string;
    public function remark() : ?string;
    public function user() : ?\Domain\User\UserInterface;
    public function contact() : ?\Domain\Person\ContactInterface;
    public function code() : ?string;
    public function nationality() : ?\Domain\Person\CountryInterface;

    public function store();
    public function delete();
}
