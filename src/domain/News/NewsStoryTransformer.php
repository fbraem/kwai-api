<?php

namespace Domain\News;

use League\Fractal;

use League\CommonMark\Converter;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use Webuni\CommonMark\TableExtension\TableExtension;

class NewsStoryTransformer extends Fractal\TransformerAbstract
{
    private $filesystem;

    private static $type = 'stories';

    private $converter = null;

    public static function createForItem(NewsStory $story, $filesystem)
    {
        return new Fractal\Resource\Item($story, new self($filesystem), self::$type);
    }

    public static function createForCollection(iterable $stories, $filesystem)
    {
        return new Fractal\Resource\Collection($stories, new self($filesystem), self::$type);
    }

    protected $defaultIncludes = [
        'category'
    ];

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

    public function transform(NewsStory $story)
    {
        $result = $story->toArray();
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
            $images = $this->filesystem->listContents('images/news/' . $story->id);
            if (count($images) > 0) {
                $result['images'] = [];
                foreach ($images as $image) {
                    $result['images'][$image['filename']] = '/files/' . $image['path'];
                }
            }
        }

        return $result;
    }

    public function includeCategory(NewsStory $story)
    {
        $category = $story->category;
        if ($category) {
            return \Domain\Category\CategoryTransformer::createForItem($category, $this->filesystem);
        }
    }
}
