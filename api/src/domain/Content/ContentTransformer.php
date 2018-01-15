<?php

namespace Domain\Content;

use League\Fractal;
use League\CommonMark\Converter;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use Webuni\CommonMark\TableExtension\TableExtension;

class ContentTransformer extends Fractal\TransformerAbstract
{
    public function transform(ContentInterface $content)
    {
        $data = $content->extract();
        if ($content->format() == 'md') {
            $environment = Environment::createCommonMarkEnvironment();
            $environment->addExtension(new TableExtension());
            $converter = new Converter(new DocParser($environment), new HtmlRenderer($environment));
            $data['html_summary'] = $converter->convertToHtml($data['summary']);
            $data['html_content'] = $converter->convertToHtml($data['content']);
        } else {
            $data['html_summary'] = $data['summary'];
            $data['html_content'] = $data['content'];
        }
        return $data;
    }
}
