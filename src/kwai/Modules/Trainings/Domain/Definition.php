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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return Entity<Team>|null
     */
    public function getTeam(): ?Entity
    {
        return $this->team;
    }

    /**
     * @return Weekday
     */
    public function getWeekday(): Weekday
    {
        return $this->weekday;
    }

    /**
     * @return Time
     */
    public function getStartTime(): Time
    {
        return $this->startTime;
    }

    /**
     * @return Time
     */
    public function getEndTime(): Time
    {
        return $this->endTime;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @return string|null
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * @return Creator
     */
    public function getCreator(): Creator
    {
        return $this->creator;
    }

    /**
     * Get the traceable time
     *
     * @return TraceableTime|null
     */
    public function getTraceableTime(): ?TraceableTime
    {
        return $this->traceableTime;
    }
}
