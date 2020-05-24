<?php
/**
 * @package Modules
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\Domain;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\ValueObjects\TraceableTime;

/**
 * The Application entity
 */
class Application implements DomainEntity
{
    private string $title;

    private string $description;

    private string $shortDescription;

    private ?string $remark = null;

    private TraceableTime $traceableTime;

    private string $application;

    private bool $canHaveNews;

    private bool $canHavePages;

    private bool $canHaveEvents;

    public function __construct(object $props)
    {
        $this->title = $props->title;
        $this->description = $props->description;
        $this->shortDescription = $props->shortDescription;
        $this->remark = $props->remark ?? null;
        $this->traceableTime = $props->traceableTime ?? new TraceableTime();
        $this->application = $props->application;
        $this->canHaveNews = $props->canHaveNews ?? true;
        $this->canHavePages = $props->canHavePages ?? true;
        $this->canHaveEvents = $props->canHaveEvents ?? true;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    /**
     * @return string|null
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * @return TraceableTime
     */
    public function getTraceableTime(): TraceableTime
    {
        return $this->traceableTime;
    }

    /**
     * @return string
     */
    public function getApplication(): string
    {
        return $this->application;
    }

    /**
     * @return bool
     */
    public function canHaveNews(): bool
    {
        return $this->canHaveNews;
    }

    /**
     * @return bool
     */
    public function canHavePages(): bool
    {
        return $this->canHavePages;
    }

    /**
     * @return bool
     */
    public function canHaveEvents(): bool
    {
        return $this->canHaveEvents;
    }
}
