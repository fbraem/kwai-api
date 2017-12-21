<?php

namespace Domain\News;

interface NewsCategoriesInterface
{
    public function find() : iterable;
    public function findOne() : ?NewsCategoryInterface;
    public function count() : int;
}
