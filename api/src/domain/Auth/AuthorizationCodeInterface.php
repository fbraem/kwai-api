<?php

namespace Domain\Auth;

interface AuthorizationCodeInterface extends \Domain\DatetimeMetaInterface, \Domain\HydratorInterface
{
    public function id() : ?int;
    public function identifier() : ?string;
    public function user();
    public function client();
    public function expiration() : string;
    public function redirectUri() : string;
    public function revoked() : boolean;

    public function store();
}
