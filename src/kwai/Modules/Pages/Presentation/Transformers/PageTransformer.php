<?php
/**
 * @package Pages
 * @subpackage Presentation
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Presentation\Transformers;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Converter\ConverterFactory;
use Kwai\Modules\Pages\Domain\Page;
use League\Fractal;

class PageTransformer extends Fractal\TransformerAbstract
{
    /**
     * The type of the page
     */
    private static string $type = 'pages';

    private ConverterFactory $converterFactory;

    /**
     * Create a singular resource of a Story entity
     *
     * @param Entity<Page>    $page
     * @param ConverterFactory $converterFactory
     * @return Fractal\Resource\Item
     */
    public static function createForItem(Entity $page, ConverterFactory $converterFactory)
    {
        return new Fractal\Resource\Item(
            $page,
            new self($converterFactory),
            self::$type
        );
    }

    /**
     * Create a collection of resources for a list of stories
     *
     * @param iterable         $pages
     * @param ConverterFactory $converterFactory
     * @return Fractal\Resource\Collection
     */
    public static function createForCollection(iterable $pages, ConverterFactory $converterFactory)
    {
        return new Fractal\Resource\Collection(
            $pages,
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
     * PageTransformer constructor.
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
     * @param Entity<Page> $page
     * @return array
     * @noinspection PhpUndefinedMethodInspection
     */
    public function transform(Entity $page): array
    {
        $traceableTime = $page->getTraceableTime();
        $result = [
            'id' => $page->id(),
            'enabled' => $page->isEnabled(),
            'remark' => $page->getRemark(),
            'created_at' => (string) $traceableTime->getCreatedAt(),
            'updated_at' => $traceableTime->isUpdated()
                ? (string) $traceableTime->getUpdatedAt() : null,
            'priority' => $page->getPriority(),
            'contents' => [],
            'images' => $page->getImages()
        ];
        foreach ($page->getContents() as $content) {
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
     * Includes the application entity
     *
     * @param Entity<Page> $page
     * @return Fractal\Resource\Item
     */
    public function includeApplication(Entity $page): Fractal\Resource\Item
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return ApplicationTransformer::createForItem($page->getApplication());
    }
}
