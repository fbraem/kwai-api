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
use Kwai\Modules\Trainings\Domain\ValueObjects\Creator;
use Kwai\Modules\Trainings\Domain\ValueObjects\Presence;
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
     * The event for the training
     */
    private Event $event;

    /**
     * The user who created the training.
     */
    private Creator $creator;

    /**
     * The coaches appointed for the training.
     * @var TrainingCoach[]
     */
    private array $coaches;

    /**
     * List of members that were present on the training.
     *
     * @var Presence[]
     */
    private array $presences;

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
     * @return Creator
     */
    public function getCreator(): Creator
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
     * @param Entity<Coach> $coach
     */
    public function releaseCoach(Entity $coach)
    {
        if (isset($this->coaches[$coach->id()])) {
            unset($this->coaches[$coach->id()]);
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

    /**
     * Register a member as present on the training.
     *
     * @param Presence $presence
     */
    public function registerPresence(Presence $presence)
    {
        if (isset($this->presences[$presence->getMember()->id()])) {
            throw new InvalidArgumentException('Member was already registered');
        }
        $this->presences[$presence->getMember()->id()] = $presence;
    }

    /**
     * Unregister a member as present on the training.
     *
     * @param Entity $member
     */
    public function unregisterPresence(Entity $member)
    {
        if (isset($this->presences[$member->id()])) {
            unset($this->presences[$member->id()]);
        }
    }

    /**
     * Get all presences.
     *
     * @return Presence[]
     */
    public function getPresences(): array
    {
        return $this->presences;
    }
}
