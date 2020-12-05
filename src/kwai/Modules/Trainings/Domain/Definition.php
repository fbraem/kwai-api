<?php
/**
 * @package Modules
 * @subpackage Training
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain;

use InvalidArgumentException;
use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Location;
use Kwai\Core\Domain\ValueObjects\Time;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Weekday;

/**
 * Class Definition
 *
 * Value object for a Definition entity.
 */
class Definition implements DomainEntity
{
    /**
     * Definition constructor.
     *
     * @param string             $name
     * @param string             $description
     * @param Weekday            $weekday
     * @param Time               $startTime
     * @param Time               $endTime
     * @param Creator            $creator
     * @param Entity<Team>|null          $team
     * @param bool               $active
     * @param Location|null      $location
     * @param string|null        $remark
     * @param TraceableTime|null $traceableTime
     */
    public function __construct(
        private string $name,
        private string $description,
        private Weekday $weekday,
        private Time $startTime,
        private Time $endTime,
        private Creator $creator,
        private ?Entity $team = null,
        private bool $active = true,
        private ?Location $location = null,
        private ?string $remark = null,
        private ?TraceableTime $traceableTime = null
    ) {
        if ($this->traceableTime == null) {
            $this->traceableTime = new TraceableTime();
        }

        if (!$this->startTime->isBefore($this->endTime)) {
            throw new InvalidArgumentException('startTime must be before endTime');
        }
    }

    /**
     * Get the name of the definition
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the description of the definition
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the associated team to this definition
     *
     * @return Entity<Team>|null
     */
    public function getTeam(): ?Entity
    {
        return $this->team;
    }

    /**
     * Get the weekday of this definition
     *
     * @return Weekday
     */
    public function getWeekday(): Weekday
    {
        return $this->weekday;
    }

    /**
     * Get the start time of this definition
     * @return Time
     */
    public function getStartTime(): Time
    {
        return $this->startTime;
    }

    /**
     * Get the end time of this definition
     *
     * @return Time
     */
    public function getEndTime(): Time
    {
        return $this->endTime;
    }

    /**
     * Is this definition still active?
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Get the location of this definition
     *
     * @return Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * Get the remark of this definition
     *
     * @return string|null
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * Get the creator of this definition
     *
     * @return Creator
     */
    public function getCreator(): Creator
    {
        return $this->creator;
    }

    /**
     * Get the traceable time of this definition
     *
     * @return TraceableTime|null
     */
    public function getTraceableTime(): ?TraceableTime
    {
        return $this->traceableTime;
    }
}
