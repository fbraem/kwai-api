<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Kwai\Core\Domain\ValueObjects\Location;
use Kwai\Core\Domain\ValueObjects\Time;
use Kwai\Core\Domain\ValueObjects\TimePeriod;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Weekday;
use Kwai\Modules\Trainings\Domain\Definition;
use Kwai\Modules\Trainings\Domain\DefinitionEntity;
use Kwai\Modules\Trainings\Infrastructure\DefinitionsTable;

/**
 * Class DefinitionDTO
 */
class DefinitionDTO
{
    public function __construct(
        public DefinitionsTable $definition = new DefinitionsTable(),
        public SeasonDTO $season = new SeasonDTO(),
        public TeamDTO $team = new TeamDTO(),
        public CreatorDTO $creator = new CreatorDTO()
    ) {
    }

    public function create(): Definition
    {
        return new Definition(
            name: $this->definition->name,
            description: $this->definition->description,
            weekday: Weekday::from($this->definition->weekday),
            period: new TimePeriod(
                start: Time::createFromString($this->definition->start_time, $this->definition->time_zone),
                end: Time::createFromString($this->definition->end_time, $this->definition->time_zone),
            ),
            creator: $this->creator->create(),
            team: $this->definition->team_id ? $this->team->createEntity() : null,
            season: $this->definition->season_id ? $this->season->createEntity() : null,
            active: $this->definition->active === 1,
            location: $this->definition->location ? new Location($this->definition->location) : null,
            remark: $this->definition->remark,
            traceableTime: new TraceableTime(
                Timestamp::createFromString($this->definition->created_at),
                $this->definition->updated_at
                    ? Timestamp::createFromString($this->definition->updated_at)
                    : null
            )
        );
    }

    public function createEntity(): DefinitionEntity
    {
        return new DefinitionEntity(
            $this->definition->id,
            $this->create()
        );
    }
}
