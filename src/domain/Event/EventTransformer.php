<?php

namespace Domain\Event;

use League\Fractal;

class EventTransformer extends Fractal\TransformerAbstract
{
    private $filesystem;

    private static $type = 'events';

    public static function createForItem(Event $event, $filesystem)
    {
        return new Fractal\Resource\Item($event, new self($filesystem), self::$type);
    }

    public static function createForCollection(iterable $events, $filesystem)
    {
        return new Fractal\Resource\Collection($events, new self($filesystem), self::$type);
    }

    protected $defaultIncludes = [
        'contents',
        'category'
    ];

    public function __construct($filesystem = null)
    {
        $this->filesystem = $filesystem;
    }

    public function transform(Event $event)
    {
        $result = $story->toArray();
        unset($result['_matchingData']);

        if ($this->filesystem) {
            $images = $this->filesystem->listContents('images/events/' . $event->id);
            if (count($images) > 0) {
                $result['images'] = [];
                foreach ($images as $image) {
                    $result['images'][$image['filename']] = '/files/' . $image['path'];
                }
            }
        }

        return $result;
    }

    public function includeContents(Event $event)
    {
        $contents = $event->contents;
        if ($contents) {
            return \Domain\Content\ContentTransformer::createForCollection($contents);
        }
    }

    public function includeCategory(Event $event)
    {
        $category = $event->category;
        if ($category) {
            return \Domain\Category\CategoryTransformer::createForItem($category, $this->filesystem);
        }
    }
}
