<?php

namespace Domain\Page;

interface PagesInterface
{
    public function find(?int $limit, ?int $offset) : iterable;
    public function findOne() : PageInterface;
    public function count() : int;
}
