<?php

namespace Domain\News;

use League\Fractal;

class NewsCategoryTransformer extends Fractal\TransformerAbstract
{
    public function transform(NewsCategoryInterface $category)
    {
        return $category->extract();
    }
}
