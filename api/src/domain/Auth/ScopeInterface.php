<?php

namespace Domain\Auth;

interface ScopeInterface extends \Domain\DatetimeMetaInterface, \Domain\HydratorInterface
{
    public function id() : ?int;
    public function identifier() : ?string;
    public function description() : ?string;

    public function store();
}
