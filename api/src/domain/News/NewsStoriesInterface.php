<?php

namespace Domain\News;

interface NewsStoriesInterface
{
    public function find(?int $limit, ?int $offset) : iterable;
    public function findOne() : ?NewsStoryInterface;
    public function count() : int;
}
