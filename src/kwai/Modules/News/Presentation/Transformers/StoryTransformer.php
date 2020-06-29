<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Presentation\Transformers;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Converter\ConverterFactory;
use Kwai\Modules\News\Domain\Story;
use League\Fractal;

class StoryTransformer extends Fractal\TransformerAbstract
{
    /**
     * The type of the story
     */
    private static string $type = 'stories';

    private ConverterFactory $converterFactory;

    /**
     * Create a singular resource of a Story entity
     *
     * @param Entity<Story>    $story
     * @param ConverterFactory $converterFactory
     * @return Fractal\Resource\Item
     */
    public static function createForItem(Entity $story, ConverterFactory $converterFactory)
    {
        return new Fractal\Resource\Item(
            $story,
            new self($converterFactory),
            self::$type
        );
    }

    /**
     * Create a collection of resources for a list of stories
     *
     * @param iterable         $stories
     * @param ConverterFactory $converterFactory
     * @return Fractal\Resource\Collection
     */
    public static function createForCollection(iterable $stories, ConverterFactory $converterFactory)
    {
        return new Fractal\Resource\Collection(
            $stories,
            new self($converterFactory),
            self::$type
        );
    }

    /**
     * The default includes
     * @var string[]
     */
    protected $defaultIncludes = [
        'application'
    ];

    /**
     * StoryTransformer constructor.
     *
     * @param ConverterFactory $converterFactory
     */
    public function __construct(ConverterFactory $converterFactory)
    {
        $this->converterFactory = $converterFactory;
    }

    /**
     * Transforms a story
     *
     * @param Entity<Story> $story
     * @return array
     * @noinspection PhpUndefinedMethodInspection
     */
    public function transform(Entity $story): array
    {
        $traceableTime = $story->getTraceableTime();
        $result = [
            'id' => $story->id(),
            'enabled' => (string) $story->isEnabled(),
            'remark' => $story->getRemark(),
            'created_at' => (string) $traceableTime->getCreatedAt(),
            'updated_at' => $traceableTime->isUpdated()
                ? (string) $traceableTime->getUpdatedAt() : null,
            'publish_date' => (string) $story->getPublishTime(),
            'timezone' => $story->getPublishTime()->getTimezone(),
            'promoted' => $story->getPromotion()->getPriority(),
            'promotion_end_date' =>
                $story->getPromotion()->getEndDate() ?
                (string) $story->getPromotion()->getEndDate() : null,
            'contents' => [],
            'images' => $story->getImages()
        ];
        foreach ($story->getContents() as $content) {
            $converter = $this->converterFactory->createConverter((string) $content->getFormat());
            $result['contents'][] = [
                'locale' => $content->getLocale(),
                'title' => $content->getTitle(),
                'summary' => $content->getSummary(),
                'content' => $content->getContent(),
                'html_summary' => $converter->convert($content->getSummary()),
                'html_content' => $converter->convert($content->getContent())
            ];
        }

        return $result;
    }

    /**
     * Includes the category entity
     *
     * @param Entity<Story> $story
     * @return Fractal\Resource\Item
     */
    public function includeApplication(Entity $story): Fractal\Resource\Item
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return ApplicationTransformer::createForItem($story->getApplication());
    }
}
