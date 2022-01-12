<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Presentation\Resources;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Converter\ConverterFactory;
use Kwai\JSONAPI;
use Kwai\Modules\News\Domain\Story;

/**
 * Class StoryResource
 *
 * A JSON:API mapper for Story
 */
#[JSONAPI\Resource(type: 'stories', id: 'getId')]
class StoryResource
{
    /**
     * @param Entity<Story>    $story
     * @param ConverterFactory $converterFactory
     */
    public function __construct(
        private Entity $story,
        private ConverterFactory $converterFactory
    ) {
    }

    public function getId(): string
    {
        return (string) $this->story->id();
    }

    #[JSONAPI\Attribute(name: 'enabled')]
    public function isEnabled(): bool
    {
        return $this->story->isEnabled();
    }

    #[JSONAPI\Attribute(name: 'remark')]
    public function getRemark(): ?string
    {
        return $this->story->getRemark();
    }

    #[JSONAPI\Attribute(name: 'created_at')]
    public function getCreationDate(): string
    {
        return (string) $this->story->getTraceableTime()->getCreatedAt();
    }

    #[JSONAPI\Attribute(name: 'updated_at')]
    public function getUpdateDate(): ?string
    {
        return $this->story->getTraceableTime()->getUpdatedAt()?->__toString();
    }

    #[JSONAPI\Attribute(name: 'publish_date')]
    public function getPublishDate(): string
    {
        return (string) $this->story->getPublishTime()->getTimestamp();
    }

    #[JSONAPI\Attribute(name: 'end_date')]
    public function getEndDate(): ?string
    {
        return $this->story->getEndDate()?->getTimestamp()->__toString();
    }

    #[JSONAPI\Attribute(name: 'timezone')]
    public function getTimezone(): string
    {
        return $this->story->getPublishtime()->getTimezone();
    }

    #[JSONAPI\Attribute(name: 'promotion')]
    public function getPromotion(): int
    {
        return $this->story->getPromotion()->getPriority();
    }

    #[JSONAPI\Attribute(name: 'promotion_end_date')]
    public function getPromotionEndDate(): ?string
    {
        return $this->story->getPromotion()->getEndDate()?->getTimestamp()->__toString();
    }

    #[JSONAPI\Attribute(name: 'contents')]
    public function getContents(): array
    {
        $contents = [];
        foreach ($this->story->getContents() as $content) {
            $converter = $this->converterFactory->createConverter($content->getFormat());
            $contents[] = [
                'locale' => $content->getLocale()->value,
                'title' => $content->getTitle(),
                'summary' => $content->getSummary(),
                'content' => $content->getContent(),
                'html_summary' => $converter->convert($content->getSummary()),
                'html_content' => $content->getContent() != null ? $converter->convert($content->getContent()) : null
            ];
        }
        return $contents;
    }

    #[JSONAPI\Attribute(name: 'images')]
    public function getImages(): array
    {
        return $this->story->getImages()->toArray();
    }

    #[JSONAPI\Relationship(name: 'application')]
    public function getApplication(): ApplicationResource
    {
        return new ApplicationResource(
            $this->story->getApplication()
        );
    }
}
