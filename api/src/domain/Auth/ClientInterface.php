<?php

namespace Domain\Auth;

interface ClientInterface extends \Domain\DatetimeMetaInterface, \Domain\HydratorInterface
{
    public function id() : ?int;
    public function name() : string;
    public function identifier() : ?string;
    public function secret() : string;
    public function redirectUri() : string;
    public function status() : int;

    public function store();
}
