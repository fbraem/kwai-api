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
    /**
     * Application constructor.
     *
     * @param string             $name
     * @param string             $title
     * @param string             $description
     * @param string             $shortDescription
     * @param string|null        $remark
     * @param TraceableTime|null $traceableTime
     * @param bool               $canHaveNews
     * @param bool               $canHavePages
     * @param bool               $canHaveEvents
     * @param int                $weight
     */
    public function __construct(
        private string $name,
        private string $title,
        private string $description,
        private string $shortDescription,
        private ?string $remark = null,
        private ?TraceableTime $traceableTime = null,
        private bool $canHaveNews = false,
        private bool $canHavePages = false,
        private bool $canHaveEvents = false,
        private int $weight = 0
    )
    {
        if ($traceableTime === null) {
            $this->traceableTime = new TraceableTime();
        }
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
    public function getName(): string
    {
        return $this->name;
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

    public function getWeight(): int
    {
        return $this->weight;
    }
}
