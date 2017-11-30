<?php

namespace Domain\Content;

use League\Fractal;

class ContentTransformer extends Fractal\TransformerAbstract
{
    public function transform(Content $content)
    {
        return [
            'id' => (int) $content->id,
            'locale' => $content->locale,
            'format' => $content->format,
            'title' => $content->title,
            'summary' => $content->summary,
            'content' => $content->content
        ];
    }
}
