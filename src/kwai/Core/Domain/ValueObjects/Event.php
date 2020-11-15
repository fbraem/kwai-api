<?php
/**
 * @package Core
 * @subpackage Domain
 */
declare(strict_types=1);

namespace Kwai\Core\Domain\ValueObjects;

/**
 * Class Event
 */
class Event
{
    /**
     * The start of the event.
     */
    private Timestamp $startDate;

    /**
     * The end of the event.
     */
    private Timestamp $endDate;

    /**
     * Is this event active?
     */
    private bool $active;

    /**
     * Is this event cancelled?
     */
    private bool $cancelled;

    /**
     * The location of the event.
     */
    private Location $location;

    /**
     * A remark
     */
    private ?string $remark;

    /**
     * Text information for the event.
     * @var Text[]
     */
    private array $text;

    /**
     * Event constructor.
     *
     * @param Timestamp   $startDate
     * @param Timestamp   $endDate
     * @param Location    $location
     * @param string|null $remark
     * @param Text[]      $text
     * @param bool        $active
     * @param bool        $cancelled
     */
    public function __construct(
        Timestamp $startDate,
        Timestamp $endDate,
        Location $location,
        ?string $remark,
        array $text,
        bool $active = true,
        bool $cancelled = false
    ) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->active = $active;
        $this->cancelled = $cancelled;
        $this->location = $location;
        $this->remark = $remark;
        $this->text = $text;
    }

    public function getStartDate(): Timestamp
    {
        return $this->startDate;
    }

    public function getEndDate(): Timestamp
    {
        return $this->endDate;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function isCancelled(): bool
    {
        return $this->cancelled;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * @return Text[]
     */
    public function getText(): array
    {
        return $this->text;
    }

    public function cancel(): void
    {
        $this->cancelled = true;
    }
}
