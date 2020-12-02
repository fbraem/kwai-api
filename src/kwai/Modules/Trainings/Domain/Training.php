<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Event;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
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
     * The coaches appointed for the training.
     *
     * @var Collection|TrainingCoach[]
     */
    private Collection $coaches;

    /**
     * The teams assigned to this training.
     *
     * @var Collection|Team[]
     */
    private Collection $teams;

    /**
     * List of members that were present on the training.
     *
     * @var Collection|Presence[]
     */
    private Collection $presences;

    /**
     * A remark
     */
    private ?string $remark;

    /**
     * The training definition that is used to create this training (if any).
     * @var Entity<TrainingDefinition>|null
     */
    private ?Entity $definition;

    /**
     * Training constructor.
     *
     * @param object $props
     */
    public function __construct(object $props)
    {
        $this->event = $props->event;
        $this->remark = $props->remark ?? null;
        $this->definition = $props->definition ?? null;
        $this->traceableTime = $props->traceableTime ?? new TraceableTime();
        $this->coaches = $props->coaches ?? new Collection();
        $this->teams = $props->teams ?? new Collection();
        $this->presences = $props->presences ?? new Collection();
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
     * @note The returned value is a copy of the collection to protect for
     *       immutability.
     * @return Collection|TrainingCoach[]
     */
    public function getCoaches(): Collection
    {
        return $this->coaches->collect();
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
     * @note The returned value is a copy of the collection to protect for
     *       immutability.
     * @return Collection|Presence[]
     */
    public function getPresences(): Collection
    {
        return $this->presences->collect();
    }

    /**
     * Get the associated teams
     *
     * @note The returned value is a copy of the collection to protect for
     *       immutability.
     * @return Collection|Team[]
     */
    public function getTeams(): Collection
    {
        return $this->teams->collect();
    }

    /**
     * Add a team to this training
     *
     * @param Entity $team
     */
    public function addTeam(Entity $team)
    {
        $this->teams[$team->id()] = $team;
    }

    /**
     * Returns the definition that was used to create this training (if any).
     *
     * @return Entity<TrainingDefinition>|null
     */
    public function getDefinition(): ?Entity
    {
        return $this->definition;
    }

    /**
     * Is this training related to a TrainingDefinition?
     */
    public function hasDefinition(): bool
    {
        return $this->definition !== null;
    }
}
