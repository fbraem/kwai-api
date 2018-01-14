<?php

namespace Domain\Category;

use League\Fractal;

class CategoryTransformer extends Fractal\TransformerAbstract
{
    public function transform(CategoryInterface $category)
    {
        return $category->extract();
    }
}
