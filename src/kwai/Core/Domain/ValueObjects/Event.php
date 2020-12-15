<?php
/**
 * @package Core
 * @subpackage Domain
 */
declare(strict_types=1);

namespace Kwai\Core\Domain\ValueObjects;

/**
 * Class Event
 *
 * Value object for an event
 */
class Event
{
    /**
     * Event constructor.
     *
     * @param Timestamp       $startDate
     * @param Timestamp       $endDate
     * @param Location|null   $location
     * @param bool            $active
     * @param bool            $cancelled
     */
    public function __construct(
        private Timestamp $startDate,
        private Timestamp $endDate,
        private ?Location $location = null,
        private bool $active = true,
        private bool $cancelled = false,
    ) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->location = $location;
        $this->active = $active;
        $this->cancelled = $cancelled;
    }

    /**
     * Get the start datetime of the event
     * @return Timestamp
     */
    public function getStartDate(): Timestamp
    {
        return $this->startDate;
    }

    /**
     * Get the end datetime of the event
     * @return Timestamp
     */
    public function getEndDate(): Timestamp
    {
        return $this->endDate;
    }

    /**
     * Is this event active?
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Is this event cancelled?
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->cancelled;
    }

    /**
     * Get the location of the event
     *
     * @return Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * Cancel the event
     */
    public function cancel(): void
    {
        $this->cancelled = true;
    }
}
