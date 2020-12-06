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
        $props = $data->select(fn($item, $key) => match ($key) {
            'name', 'description' => $item,
            'season' => SeasonMapper::toDomain($item),
            'weekday' => new Weekday((int) $item),
            'start_time' => [
                'startTime',
                Time::createFromString($item, $data->get('time_zone'))
            ],
            'end_time' => [
                'endTime',
                Time::createFromString($item, $data->get('time_zone'))
            ],
            'team' => TeamMapper::toDomain($item),
            'active' => $item === '1',
            'location' => new Location($item),
            'creator' => new Creator(
                id: (int) $item->get('id'),
                name: new Name(
                    first_name: $item->get('first_name'),
                    last_name: $item->get('last_name')
                )
            ),
            'created_at' => ['traceableTime' => new TraceableTime(
                Timestamp::createFromString($item),
                $data->has('updated_at')
                    ? Timestamp::createFromString($data->get('updated_at'))
                    : null
            )],
            default => null
        })->filter();

        return new Entity(
            (int) $data['id'],
            new Definition(... $props->toArray())
        );
    }
}
