<?php

namespace Domain\User;

interface UserInterface extends \Domain\DatetimeMetaInterface, \Domain\HydratorInterface
{
    // Properties
    public function id() : ?int;
    public function email() : string;
    public function password() : string;
    public function lastLogin() : ?string;
    public function firstName() : ?string;
    public function lastName() : ?string;
    public function remark() : ?string;

    // Methods
    public function store();
    public function verify(string $password) : bool;
}
