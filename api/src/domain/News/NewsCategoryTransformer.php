<?php

namespace Domain\News;

use League\Fractal;

class NewsCategoryTransformer extends Fractal\TransformerAbstract
{
    public function transform(NewsCategory $category)
    {
        return [
            'id' => (int) $category->id,
            'name' => $category->name,
            'description' => $category->description,
            'remark' => $category->remark
        ];
    }
}
