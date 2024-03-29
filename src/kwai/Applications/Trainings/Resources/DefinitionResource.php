<?php
/**
 * @package Applications
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Applications\Trainings\Resources;

use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\DefinitionEntity;

/**
 * Class DefinitionResource
 */
#[JSONAPI\Resource(type: 'definitions', id: 'getId')]
class DefinitionResource
{
    public function __construct(
        private DefinitionEntity $definition
    ) {
    }

    public function getId(): string
    {
        return (string) $this->definition->id();
    }

    #[JSONAPI\Attribute(name: 'name')]
    public function getName(): string
    {
        return $this->definition->getName();
    }

    #[JSONAPI\Attribute(name: 'description')]
    public function getDescription(): string
    {
        return $this->definition->getDescription();
    }

    #[JSONAPI\Attribute(name: 'weekday')]
    public function getWeekday(): int
    {
        return $this->definition->getWeekday()->value;
    }

    #[JSONAPI\Attribute(name: 'start_time')]
    public function getStartTime(): string
    {
        return (string) $this->definition->getPeriod()->getStart();
    }

    #[JSONAPI\Attribute(name: 'end_time')]
    public function getEndTime(): string
    {
        return (string) $this->definition->getPeriod()->getEnd();
    }

    #[JSONAPI\Attribute(name: 'time_zone')]
    public function getTimezone(): string
    {
        return $this->definition->getPeriod()->getStart()->getTimezone();
    }

    #[JSONAPI\Attribute(name: 'active')]
    public function isActive(): bool
    {
        return $this->definition->isActive();
    }

    #[JSONAPI\Attribute(name: 'location')]
    public function getLocation(): ?string
    {
        return $this->definition->getLocation()?->__toString();
    }

    #[JSONAPI\Attribute(name: 'remark')]
    public function getRemark(): ?string
    {
        return (string) $this->definition->getRemark();
    }

    #[JSONAPI\Attribute(name: 'created_at')]
    public function getCreationDate(): string
    {
        return (string) $this->definition->getTraceableTime()->getCreatedAt();
    }

    #[JSONAPI\Attribute(name: 'updated_at')]
    public function getUpdateDate(): ?string
    {
        return $this->definition->getTraceableTime()->getUpdatedAt()?->__toString();
    }

    #[JSONAPI\Relationship(name: 'team')]
    public function getTeam(): ?TeamResource
    {
        $team = $this->definition->getTeam();
        if ($team) {
            return new TeamResource($team);
        }
        return null;
    }
}
