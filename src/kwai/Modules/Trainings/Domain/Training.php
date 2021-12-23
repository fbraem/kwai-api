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
use Kwai\Core\Domain\ValueObjects\Text;
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
     * Training constructor.
     *
     * @param Event                   $event
     * @param Collection|null         $text
     * @param string|null             $remark
     * @param Entity<Definition>|null $definition
     * @param TraceableTime|null      $traceableTime
     * @param Collection|null         $coaches
     * @param Collection|null         $teams
     * @param Collection|null         $presences
     */
    public function __construct(
        private Event $event,
        private ?Collection $text = null,
        private ?string $remark = null,
        private ?Entity $definition = null,
        private ?TraceableTime $traceableTime = null,
        private ?Collection $coaches = null,
        private ?Collection $teams = null,
        private ?Collection $presences = null
    ) {
        $this->text ??= new Collection();
        $this->traceableTime ??= new TraceableTime();
        $this->coaches ??= new Collection();
        $this->teams ??= new Collection();
        $this->presences ??= new Collection();
    }

    /**
     * Get the create_at/update_at timestamps
     *
     * @return TraceableTime
     */
    public function getTraceableTime(): TraceableTime
    {
        return $this->traceableTime;
    }

    /**
     * Get the event
     *
     * @return Event
     */
    public function getEvent(): Event
    {
        return $this->event;
    }

    /**
     * Get the remark
     *
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
     * @return Collection
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
     * @return Collection
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
     * @return Collection
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
     * @return Entity<Definition>|null
     */
    public function getDefinition(): ?Entity
    {
        return $this->definition;
    }

    /**
     * Is this training related to a Definition?
     */
    public function hasDefinition(): bool
    {
        return $this->definition !== null;
    }

    /**
     * Returns a copy of the associated text.
     *
     * @return Collection|null
     */
    public function getText(): ?Collection
    {
        return $this->text->collect();
    }

    /**
     * Add text
     *
     * @param Text $text
     */
    public function addText(Text $text)
    {
        $this->text->put($text->getLocale(), $text);
    }
}
