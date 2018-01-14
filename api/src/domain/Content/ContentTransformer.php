<?php

namespace Domain\Content;

use League\Fractal;

class ContentTransformer extends Fractal\TransformerAbstract
{
    public function transform(ContentInterface $content)
    {
        $data = $content->extract();
        if ($content->format() == 'md') {
            $data['html_summary'] = \Parsedown::instance()->line($data['summary']);
            $data['html_content'] = \Parsedown::instance()->text($data['content']);
        } else {
            $data['html_summary'] = $data['summary'];
            $data['html_content'] = $data['content'];
        }
        return $data;
    }
}
