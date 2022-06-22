<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain\ValueObjects;

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Modules\Trainings\Domain\MemberEntity;

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
     * @param MemberEntity       $member
     * @param Creator            $creator
     * @param string|null        $remark
     * @param TraceableTime|null $traceableTime
     */
    public function __construct(
        private MemberEntity $member,
        private Creator $creator,
        private ?string $remark = null,
        private ?TraceableTime $traceableTime = new TraceableTime()
    ) {
    }

    public function getMember(): MemberEntity
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
