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
use Kwai\Modules\Trainings\Domain\TrainingDefinition;

/**
 * Class TrainingDefinitionMapper
 */
class TrainingDefinitionMapper
{
    /**
     * Maps a table record to the Training domain entity.
     *
     * @param Collection $data
     * @return Entity<TrainingDefinition>
     */
    public static function toDomain(Collection $data): Entity
    {
        return new Entity(
            (int)$data['id'],
            new TrainingDefinition((object)[
                'name' => $data->get('name'),
                'description' => $data->get('description'),
                /* 'team' => new Team() */
                'weekday' => new Weekday((int) $data->get('weekday')),
                'start_time' => Time::createFromString(
                    $data->get('start_time'),
                    $data->get('time_zone')
                ),
                'end_time' => Time::createFromString(
                    $data->get('end_time'),
                    $data->get('time_zone')
                ),
                'active' => $data->get('active', '1') === '1',
                'location' => $data->has('location')
                    ? new Location($data->get('location'))
                    : null,
                'creator' => new Creator(
                    (int) $data['creator']['id'],
                    new Name(
                        $data['creator']->get('first_name'),
                        $data['creator']->get('last_name')
                    )
                ),
                'traceableTime' => new TraceableTime(
                    Timestamp::createFromString($data['created_at']),
                    $data->has('updated_at')
                        ? Timestamp::createFromString($data->get('updated_at'))
                        : null
                ),
            ])
        );
    }
}
