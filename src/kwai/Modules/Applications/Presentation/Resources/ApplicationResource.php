<?php
/**
 * @package Modules
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\Presentation\Resources;

use Kwai\Core\Domain\Entity;
use Kwai\JSONAPI;
use Kwai\Modules\Applications\Domain\Application;

/**
 * Class ApplicationResource
 *
 * A JSON:API resource for Application entity.
 */
#[JSONAPI\Resource(type: 'applications', id: 'getId')]
class ApplicationResource
{
    /**
     * @param Entity<Application> $application
     */
    public function __construct(
        private Entity $application
    ) {
    }

    public function getId(): string
    {
        return (string) $this->application->id();
    }

    #[JSONAPI\Attribute(name: 'title')]
    public function getTitle(): string
    {
        return $this->application->getTitle();
    }

    #[JSONAPI\Attribute(name: 'name')]
    public function getName(): string
    {
        return $this->application->getName();
    }

    #[JSONAPI\Attribute(name: 'short_description')]
    public function getShortDescription(): string
    {
        return $this->application->getShortDescription();
    }

    #[JSONAPI\Attribute(name: 'description')]
    public function getDescription(): string
    {
        return $this->application->getDescription();
    }

    #[JSONAPI\Attribute(name: 'remark')]
    public function getRemark(): ?string
    {
        return $this->application->getRemark();
    }

    #[JSONAPI\Attribute(name: 'weight')]
    public function getWeight(): int
    {
        return $this->application->getWeight();
    }

    #[JSONAPI\Attribute(name: 'news')]
    public function getNews(): bool
    {
        return $this->application->canHaveNews();
    }

    #[JSONAPI\Attribute(name: 'pages')]
    public function getPages(): bool
    {
        return $this->application->canHavePages();
    }

    #[JSONAPI\Attribute(name: 'events')]
    public function getEvents(): bool
    {
        return $this->application->canHaveEvents();
    }

    #[JSONAPI\Attribute(name: 'created_at')]
    public function getCreatedAt(): string
    {
        return (string) $this->application->getTraceableTime()->getCreatedAt();
    }

    #[JSONAPI\Attribute(name: 'updated_at')]
    public function getUpdatedAt(): ?string
    {
        return $this->application->getTraceableTime()->getUpdatedAt()?->__toString();
    }
}
