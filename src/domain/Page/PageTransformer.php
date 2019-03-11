<?php

namespace Domain\Page;

use League\Fractal;

use League\CommonMark\Converter;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use Webuni\CommonMark\TableExtension\TableExtension;

class PageTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'pages';

    private $filesystem;

    private $converter = null;

    protected $defaultIncludes = [
        'category'
    ];

    public static function createForItem(Page $page, $filesystem)
    {
        return new Fractal\Resource\Item($page, new self($filesystem), self::$type);
    }

    public static function createForCollection(iterable $pages, $filesystem)
    {
        return new Fractal\Resource\Collection($pages, new self($filesystem), self::$type);
    }

    public function __construct($filesystem = null)
    {
        $this->filesystem = $filesystem;

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new TableExtension());
        $this->converter = new Converter(
            new DocParser($environment),
            new HtmlRenderer($environment)
        );
    }

    public function transform(Page $page)
    {
        $result = $page->toArray();
        unset($result['_matchingData']);

        foreach ($result['contents'] as &$content) {
            unset($content['id']);
            unset($content['_joinData']);
            if ($content['format'] == 'md') {
                $content['html_summary']
                    = $this->converter->convertToHtml($content['summary']);
                $content['html_content']
                    = $this->converter->convertToHtml($content['content']);
            } else {
                $content['html_summary'] = $content['summary'];
                $content['html_content'] = $content['content'];
            }
        }

        if ($this->filesystem) {
            $images = $this->filesystem->listContents('images/pages/' . $page->id);
            if (count($images) > 0) {
                $result['images'] = [];
                foreach ($images as $image) {
                    $result['images'][$image['filename']] = '/files/' . $image['path'];
                }
            }
        }
        return $result;
    }

    public function includeCategory(Page $page)
    {
        $category = $page->category;
        if ($category) {
            return \Domain\Category\CategoryTransformer::createForItem($category);
        }
    }
}
