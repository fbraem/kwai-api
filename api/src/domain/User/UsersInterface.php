<?php

namespace Domain\User;

interface UsersInterface
{
    public function find() : iterable;
    public function findOne() : UserInterface;
    public function count() : int;
}
