<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Time;
use Kwai\Core\Domain\ValueObjects\TimePeriod;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Weekday;
use Kwai\Core\Infrastructure\Mappers\CreatorMapper;
use Kwai\Modules\Trainings\Domain\Definition;

/**
 * Class DefinitionMapper
 */
class DefinitionMapper
{
    /**
     * Maps a table record to the Training domain entity.
     *
     * @param Collection $data
     * @return Definition
     */
    public static function toDomain(Collection $data): Definition
    {
        return new Definition(
            name: $data->get('name'),
            description: $data->get('description'),
            season: $data->has('season')
                ? new Entity((int) $data->get('season')->get('id'), SeasonMapper::toDomain($data->get('season')))
                : null,
            team: $data->has('team')
                ? new Entity((int) $data->get('team')->get('id'), TeamMapper::toDomain($data->get('team')))
                : null,
            weekday: new Weekday((int) $data->get('weekday')),
            period: new TimePeriod(
                start: Time::createFromString($data->get('start_time'), $data->get('time_zone')),
                end: Time::createFromString($data->get('end_time'), $data->get('time_zone')),
            ),
            active: $data->get('active') === '1',
            creator: CreatorMapper::toDomain($data->get('creator')),
            traceableTime: new TraceableTime(
            Timestamp::createFromString($data->get('created_at')),
            $data->has('updated_at')
                ? Timestamp::createFromString($data->get('updated_at'))
                : null
            )
        );
    }

    /**
     * Maps a Definition domain to the table record
     *
     * @param Definition $definition
     * @return Collection
     */
    public static function toPersistence(Definition $definition): Collection
    {
        return collect([
            'name' => $definition->getName(),
            'description' => $definition->getDescription(),
            'season_id' => $definition->getSeason()?->id(),
            'team_id' => $definition->getTeam()?->id(),
            'weekday' => $definition->getWeekday(),
            'start_time' => $definition->getPeriod()->getStart(),
            'end_time' => $definition->getPeriod()->getEnd(),
            'time_zone' => $definition->getPeriod()->getEnd()->getTimezone(),
            'active' => $definition->isActive(),
            'location' => $definition->getLocation(),
            'remark' => $definition->getRemark(),
            'creator' => CreatorMapper::toPersistence($definition->getCreator()),
            'created_at' => $definition->getTraceableTime()->getCreatedAt(),
            'updated_at' => $definition->getTraceableTime()->getUpdatedAt(),
        ]);
    }
}
