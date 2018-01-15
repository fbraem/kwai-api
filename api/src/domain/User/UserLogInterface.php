<?php

namespace Domain\User;

interface UserLogInterface extends \Domain\DatetimeMetaInterface
{
    // Properties
    public function id() : ?int;
    public function user() : UserInterface;
    public function action() : string;
    public function rest() : string;
    public function modelId() : int;

    // Methods
    public function store();
}
