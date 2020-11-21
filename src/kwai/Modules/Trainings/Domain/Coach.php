<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Modules\Trainings\Domain\ValueObjects\Creator;

/**
 * Class Coach
 *
 * Entity that represents a Coach
 */
class Coach implements DomainEntity
{
    /**
     * A description
     */
    private string $description;

    /**
     * The diploma of the coach
     */
    private ?string $diploma;

    /**
     * Is this coach still active?
     */
    private bool $active;

    /**
     * A remark
     */
    private ?string $remark;

    /**
     * The create/update timestamps
     */
    private TraceableTime $traceableTime;

    /**
     * The user who created this coach.
     *
     * @var Creator
     */
    private Creator $creator;

    /**
     * A coach is also a member
     * @var Entity<Member>
     */
    private Entity $member;

    /**
     * Coach constructor.
     *
     * @param object $props
     */
    public function __construct(object $props)
    {
        $this->description = $props->description;
        $this->diploma = $props->diploma ?? null;
        $this->active = $props->active;
        $this->remark = $props->remark ?? null;
        $this->traceableTime = $props->traceableTime ?? new TraceableTime();
        $this->creator = $props->creator;
        $this->member = $props->member;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return ?string
     */
    public function getDiploma(): ?string
    {
        return $this->diploma;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
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
     * @return Creator
     */
    public function getCreator(): Creator
    {
        return $this->creator;
    }

    /**
     * @return Entity
     */
    public function getMember(): Entity
    {
        return $this->member;
    }
}
