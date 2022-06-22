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
     * Presence constructor.
     *
     * @param Entity<Member>     $member
     * @param Creator            $creator
     * @param string|null        $remark
     * @param TraceableTime|null $traceableTime
     */
    public function __construct(
        private Entity $member,
        private Creator $creator,
        private ?string $remark = null,
        private ?TraceableTime $traceableTime = new TraceableTime()
    ) {
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
