<?php

namespace Domain\User;

interface UserLogsInterface
{
    public function find() : iterable;
    public function findOne() : UserLogInterface;
    public function count() : int;
}
