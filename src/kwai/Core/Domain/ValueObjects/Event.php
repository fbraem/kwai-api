<?php
/**
 * @package Core
 * @subpackage Domain
 */
declare(strict_types=1);

namespace Kwai\Core\Domain\ValueObjects;

use Illuminate\Support\Collection;

/**
 * Class Event
 *
 * Value object for an event
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
    private ?Location $location;

    /**
     * A remark
     */
    private ?string $remark;

    /**
     * Text information for the event.
     *
     * @var Collection
     */
    private Collection $text;

    /**
     * Event constructor.
     *
     * @param Timestamp       $startDate
     * @param Timestamp       $endDate
     * @param Location|null   $location
     * @param Collection|null $text
     * @param bool            $active
     * @param bool            $cancelled
     * @param string|null     $remark
     */
    public function __construct(
        Timestamp $startDate,
        Timestamp $endDate,
        ?Location $location = null,
        ?Collection $text = null,
        bool $active = true,
        bool $cancelled = false,
        ?string $remark = null
    ) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->location = $location;
        $this->text = $text ?? new Collection();
        $this->active = $active;
        $this->cancelled = $cancelled;
        $this->remark = $remark;
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

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * @return Collection
     */
    public function getText(): Collection
    {
        return $this->text->collect();
    }

    /**
     * Cancel the event
     */
    public function cancel(): void
    {
        $this->cancelled = true;
    }

    /**
     * Add text content to the event.
     *
     * @param Text $text
     */
    public function addText(Text $text)
    {
        $this->text->push($text);
    }
}
