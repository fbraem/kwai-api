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
        $props = $data->only(['name', 'description']);
        // TODO:
        // if ($data->has('team')) {
        // }
        $props->put('weekday', new Weekday((int) $data['weekday']));
        $props->put('start_time', Time::createFromString(
            $data->get('start_time'),
            $data->get('time_zone')
        ));
        $props->put('end_time', Time::createFromString(
            $data->get('end_time'),
            $data->get('time_zone')
        ));
        $props->put('active', $data->get('active', '1') === '1');
        if ($data->has('location')) {
            $props->put(
                'location',
                new Location($data->get('location'))
            );
        }
        $props->put('creator', new Creator(
            id: (int) $data['creator']['id'],
            name: new Name(
                first_name: $data['creator']->get('first_name'),
                last_name: $data['creator']->get('last_name')
            )
        ));
        $props->put('traceableTime', new TraceableTime(
            Timestamp::createFromString($data['created_at']),
            $data->has('updated_at')
                ? Timestamp::createFromString($data->get('updated_at'))
                : null
        ));

        return new Entity(
            (int) $data['id'],
            new Definition(... $props->toArray())
        );
    }
}
