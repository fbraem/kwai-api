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
use Kwai\Modules\Trainings\Domain\Coach;

/**
 * Class TrainingCoach
 *
 * A value object for a coach that is appointed to a training.
 */
class TrainingCoach
{
    /**
     * TrainingCoach constructor.
     *
     * @param Entity<Coach>      $coach
     * @param bool               $head
     * @param bool               $present
     * @param bool               $payed
     * @param Creator            $creator
     * @param ?string            $remark
     * @param TraceableTime|null $traceableTime
     */
    public function __construct(
        private Entity $coach,
        private Creator $creator,
        private bool $head = false,
        private bool $present = false,
        private bool $payed = false,
        private ?string $remark = null,
        private ?TraceableTime $traceableTime = null
    ) {
        $this->traceableTime ??= new TraceableTime();
    }

    /**
     * The coach
     *
     * @return Entity<Coach>
     */
    public function getCoach(): Entity
    {
        return $this->coach;
    }

    /**
     * Is the coach the head coach of this training?
     *
     * @return bool
     */
    public function isHead(): bool
    {
        return $this->head;
    }

    /**
     * Was the coach present at this training?
     *
     * @return bool
     */
    public function isPresent(): bool
    {
        return $this->present;
    }

    /**
     * Is the coach payed for this training?
     *
     * @return bool
     */
    public function isPayed(): bool
    {
        return $this->payed;
    }

    /**
     * A remark
     *
     * @return string|null
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * The user that changed or added this coach
     *
     * @return Creator
     */
    public function getCreator(): Creator
    {
        return $this->creator;
    }

    /**
     * @return TraceableTime|null
     */
    public function getTraceableTime(): ?TraceableTime
    {
        return $this->traceableTime;
    }
}
