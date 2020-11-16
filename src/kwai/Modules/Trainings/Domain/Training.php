<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain;

use InvalidArgumentException;
use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Event;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Modules\Trainings\Domain\ValueObjects\TrainingCoach;

/**
 * Class Training
 *
 * Entity that represents a Training.
 */
class Training implements DomainEntity
{
    /**
     * The create/update timestamps
     */
    private TraceableTime $traceableTime;

    /**
     * The event of this training
     */
    private Event $event;

    /**
     * The user who created this training.
     * @var Entity<Creator>
     */
    private Entity $creator;

    /**
     * The coaches appointed for this training.
     * @var TrainingCoach[]
     */
    private array $coaches;

    /**
     * A remark
     */
    private ?string $remark;

    /**
     * Training constructor.
     *
     * @param object $props
     */
    public function __construct(object $props)
    {
        $this->event = $props->event;
        $this->creator = $props->creator;
        $this->remark = $props->remark ?? null;
        $this->traceableTime = $props->traceableTime ?? new TraceableTime();
    }

    /**
     * @return TraceableTime
     */
    public function getTraceableTime(): TraceableTime
    {
        return $this->traceableTime;
    }

    /**
     * @return Event
     */
    public function getEvent(): Event
    {
        return $this->event;
    }

    /**
     * @return Entity<Creator>
     */
    public function getCreator(): Entity
    {
        return $this->creator;
    }

    /**
     * @return string|null
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * Appoint a coach to the training. It's possible to appoint multiple
     * coaches.
     *
     * @param TrainingCoach $coach
     */
    public function appointCoach(TrainingCoach $coach)
    {
        if (isset($this->coaches[$coach->getCoach()->id()])) {
            throw new InvalidArgumentException('Coach already appointed.');
        }
        $this->coaches[$coach->getCoach()->id()] = $coach;
    }

    /**
     * Remove a coach from the training.
     *
     * @param TrainingCoach $coach
     */
    public function releaseCoach(TrainingCoach $coach)
    {
        if (isset($this->coaches[$coach->getCoach()->id()])) {
            unset($this->coaches[$coach->getCoach()->id()]);
        }
    }

    /**
     * Get all coaches appointed to the training.
     *
     * @return TrainingCoach[]
     */
    public function getCoaches(): array
    {
        return $this->coaches;
    }
}
