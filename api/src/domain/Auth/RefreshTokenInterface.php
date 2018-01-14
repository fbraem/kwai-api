<?php

namespace Domain\Auth;

interface RefreshTokenInterface extends \Domain\DatetimeMetaInterface
{
    public function id() : ?int;
    public function identifier() : ?string;
    public function accessToken();
    public function expiration();
    public function revoked();

    public function store();
    public function revoke();
}
