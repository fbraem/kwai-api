<?php

namespace Domain\Category;

interface CategoriesInterface
{
    public function find() : iterable;
    public function findOne() : ?CategoryInterface;
    public function count() : int;
}
