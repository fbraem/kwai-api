<?php

namespace Domain\Content;

use League\Fractal;
use League\CommonMark\Converter;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use League\CommonMark\Ext\Table\TableExtension;

class ContentTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'contents';

    protected $defaultIncludes = [
        'user'
    ];

    public static function createForItem(Content $content)
    {
        return new Fractal\Resource\Item($content, new self(), self::$type);
    }

    public static function createForCollection(iterable $contents)
    {
        return new Fractal\Resource\Collection($contents, new self(), self::$type);
    }

    public function transform(Content $content)
    {
        $data = $content->toArray();

        unset($data['_joinData']);

        if ($content->format == 'md') {
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

    public function includeUser(Content $content)
    {
        $author = $content->user;
        if ($author) {
            return \Domain\User\UserTransformer::createForItem($author);
        }
    }
}
