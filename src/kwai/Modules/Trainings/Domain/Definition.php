<?php
/**
 * @package Modules
 * @subpackage Training
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Location;
use Kwai\Core\Domain\ValueObjects\Time;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\Weekday;

/**
 * Class Definition
 *
 * Value object for a Definition entity.
 */
class Definition implements DomainEntity
{
    /**
     * The name of the definition
     */
    private string $name;

    /**
     * A description
     */
    private string $description;

    /**
     * A definition for a team?
     */
    private ?Team $team;

    /**
     * Weekday
     */
    private Weekday $weekday;

    /**
     * Starttime
     */
    private Time $start_time;

    /**
     * Endtime
     */
    private Time $end_time;

    /**
     * Is this definition active?
     */
    private bool $active;

    /**
     * The location of the training
     */
    private ?Location $location;

    /**
     * A remark
     */
    private ?string $remark;

    /**
     * Creator of the definition
     */
    private Creator $creator;

    /**
     * Definition constructor.
     *
     * @param object $props
     */
    public function __construct(object $props)
    {
        $this->name = $props->name;
        $this->description = $props->description;
        $this->team = $props->team ?? null;
        $this->weekday = $props->weekday;
        $this->start_time = $props->start_time;
        $this->end_time = $props->end_time;
        $this->active = $props->active ?? true;
        $this->location = $props->location ?? null;
        $this->remark = $props->remark ?? null;
        $this->creator = $props->creator;
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
     * @return Team|null
     */
    public function getTeam(): ?Team
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
        return $this->start_time;
    }

    /**
     * @return Time
     */
    public function getEndTime(): Time
    {
        return $this->end_time;
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
}
