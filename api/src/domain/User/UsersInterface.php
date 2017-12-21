<?php

namespace Domain\User;

interface UsersInterface
{
    function find() : iterable;
    function count() : int;
}
