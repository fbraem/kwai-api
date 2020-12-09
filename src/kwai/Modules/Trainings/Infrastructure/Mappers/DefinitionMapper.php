<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Location;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Time;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Weekday;
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
     * @return Entity<Definition>
     */
    public static function toDomain(Collection $data): Entity
    {
        $props = $data->transformWithKeys(fn($item, $key) => match ($key) {
            'name', 'description' => true,
            'season' => [ $key => SeasonMapper::toDomain($item) ],
            'weekday' => [ $key => new Weekday((int) $item) ],
            'start_time' => [
                'startTime' =>
                Time::createFromString($item, $data->get('time_zone'))
            ],
            'end_time' => [
                'endTime' =>
                Time::createFromString($item, $data->get('time_zone'))
            ],
            'team' => [ $key => TeamMapper::toDomain($item) ],
            'active' => [ $key => $item === '1' ],
            'location' => [ $key => new Location($item) ],
            'creator' => [ $key => new Creator(
                id: (int) $item->get('id'),
                name: new Name(
                    first_name: $item->get('first_name'),
                    last_name: $item->get('last_name')
                )
            )],
            'created_at' => ['traceableTime' => new TraceableTime(
                Timestamp::createFromString($item),
                $data->has('updated_at')
                    ? Timestamp::createFromString($data->get('updated_at'))
                    : null
            )],
            default => false
        });

        return new Entity(
            (int) $data['id'],
            new Definition(... $props)
        );
    }

    /**
     * Maps a Definition domain to the table record
     *
     * @param Definition $definition
     * @return string[]
     */
    public static function toPersistence(Definition $definition)
    {
        return [
            'name' => $definition->getName(),
            'description' => $definition->getDescription(),
            'season_id' => $definition->getSeason()?->id(),
            'team_id' => $definition->getTeam()?->id(),
            'weekday' => $definition->getWeekday(),
            'start_time' => $definition->getStartTime(),
            'end_time' => $definition->getEndTime(),
            'time_zone' => $definition->getStartTime()->getTimezone(),
            'active' => $definition->isActive(),
            'location' => $definition->getLocation(),
            'remark' => $definition->getRemark(),
            'user_id' => $definition->getCreator()->getId(),
            'created_at' => $definition->getTraceableTime()->getCreatedAt(),
            'updated_at' => $definition->getTraceableTime()->getUpdatedAt(),
        ];
    }
}
