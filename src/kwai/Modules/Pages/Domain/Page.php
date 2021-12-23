<?php
/**
 * @package Pages
 * @subpackage Domain
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Domain;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\TraceableTime;

/**
 * The Page entity
 */
class Page implements DomainEntity
{
    /**
     * Page constructor.
     *
     * @param Entity             $application
     * @param bool               $enabled
     * @param Collection|null    $contents
     * @param Collection|null    $images
     * @param int                $priority
     * @param string|null        $remark
     * @param TraceableTime|null $traceableTime
     */
    public function __construct(
        private Entity $application,
        private bool $enabled = false,
        private ?Collection $contents = null,
        private ?Collection $images = null,
        private int $priority = 0,
        private ?string $remark = null,
        private ?TraceableTime $traceableTime = null
    ) {
        $this->contents ??= new Collection();
        $this->images ??= new Collection();
        $this->traceableTime ??= new TraceableTime();
    }

    /**
     * Attach images
     *
     * @param Collection $images
     */
    public function attachImages(Collection $images)
    {
        $this->images->merge($images);
    }

    /**
     * Add content
     *
     * @param Text $content
     */
    public function addContent(Text $content)
    {
        $this->contents->push($content);
    }

    /**
     * Is this page enabled?
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Get the contents
     *
     * @return Collection
     */
    public function getContents(): Collection
    {
        return $this->contents->collect();
    }

    /**
     * Get all attached images
     *
     * @return Collection
     */
    public function getImages(): Collection
    {
        return $this->images->collect();
    }

    /**
     * Get the priority
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Get the remark
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * Return the associated application
     *
     * @return Entity<Application>
     */
    public function getApplication(): Entity
    {
        return $this->application;
    }

    /**
     * Get the create/update timestamps
     */
    public function getTraceableTime(): TraceableTime
    {
        return $this->traceableTime;
    }
}
