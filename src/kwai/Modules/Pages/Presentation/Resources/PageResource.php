<?php
/**
 * @package Modules
 * @subpackage Pages
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Presentation\Resources;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Converter\ConverterFactory;
use Kwai\JSONAPI;
use Kwai\Modules\Pages\Domain\Page;

/**
 * Class PageResource
 *
 * A JSON:API mapper for Page entity
 */
#[JSONAPI\Resource(type:'pages', id: 'getId')]
class PageResource
{
    /**
     * @param Entity<Page>     $page
     * @param ConverterFactory $converterFactory
     */
    public function __construct(
        private Entity $page,
        private ConverterFactory $converterFactory
    ) {
    }

    public function getId(): string
    {
        return (string) $this->page->id();
    }

    #[JSONAPI\Attribute(name: 'enabled')]
    public function isEnabled(): bool
    {
        return $this->page->isEnabled();
    }

    #[JSONAPI\Attribute(name: 'remark')]
    public function getRemark(): ?string
    {
        return $this->page->getRemark();
    }

    #[JSONAPI\Attribute(name: 'created_at')]
    public function getCreatedAt(): string
    {
        return (string) $this->page->getTraceableTime()->getCreatedAt();
    }

    #[JSONAPI\Attribute(name: 'updated_at')]
    public function getUpdatedAt(): ?string
    {
        return $this->page->getTraceableTime()->getUpdatedAt()?->__toString();
    }

    #[JSONAPI\Attribute(name: 'priority')]
    public function getPriority(): int
    {
        return $this->page->getPriority();
    }

    #[JSONAPI\Attribute(name: 'images')]
    public function getImages(): array
    {
        return $this->page->getImages()->toArray();
    }

    #[JSONAPI\Attribute(name: 'contents')]
    public function getContents(): array
    {
        $contents = [];
        foreach ($this->page->getContents() as $content) {
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

    #[JSONAPI\Relationship(name: 'application')]
    public function getApplication(): ApplicationResource
    {
        return new ApplicationResource(
            $this->page->getApplication()
        );
    }
}
