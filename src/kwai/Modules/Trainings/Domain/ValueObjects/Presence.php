<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain\ValueObjects;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Modules\Trainings\Domain\Member;

/**
 * Class Presence
 *
 * A value object for a presence of a member at the training.
 */
class Presence
{
    /**
     * @var Entity<Member>
     */
    private Entity $member;

    private ?string $remark;

    private Creator $creator;

    private TraceableTime $traceableTime;

    /**
     * Presence constructor.
     *
     * @param Entity<Member>     $member
     * @param Creator            $creator
     * @param string|null        $remark
     * @param TraceableTime|null $traceableTime
     */
    public function __construct(
        Entity $member,
        Creator $creator,
        ?string $remark = null,
        TraceableTime $traceableTime = null
    ) {
        $this->member = $member;
        $this->remark = $remark ?? null;
        $this->creator = $creator;
        $this->traceableTime = $traceableTime ?? new TraceableTime();
    }

    /**
     * @return Entity<Member>
     */
    public function getMember(): Entity
    {
        return $this->member;
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
     * @return TraceableTime
     */
    public function getTraceableTime(): TraceableTime
    {
        return $this->traceableTime;
    }
}
