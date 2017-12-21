<?php

namespace Domain\Auth;

interface AccessTokenInterface extends \Domain\DatetimeMetaInterface
{
    function id() : ?int;
    function identifier() : ?string;
    function client();
    function user();
    function expiration();
    function revoked();
    function type();
    function scopes();

    function store();
    function revoke();
}
