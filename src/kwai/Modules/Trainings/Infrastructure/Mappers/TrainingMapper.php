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
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Event;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Domain\ValueObjects\Location;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Modules\Trainings\Domain\Training;

/**
 * Class TrainingMapper
 */
class TrainingMapper
{
    /**
     * Maps a table record to the Training domain entity.
     *
     * @param Collection $data
     * @return Entity<Training>
     */
    public static function toDomain(Collection $data): Entity
    {
        $props = $data->transformWithKeys(fn($item, $key) => match ($key) {
            'remark' => true,
            'teams' => [ 'teams' => $item->map(fn ($team, $key) => TeamMapper::toDomain($team)) ],
            'coaches' => [ 'coaches' => $item->map(fn ($coach, $key) => CoachMapper::toDomain($coach)) ],
            'definition' => [ 'definition' => DefinitionMapper::toDomain($item) ],
            'start_date' => [
                'event' => new Event(
                    startDate: Timestamp::createFromString($data['start_date'], $data['time_zone']),
                    endDate: Timestamp::createFromString($data['end_date'], $data['time_zone']),
                    location: $data->has('location') ? new Location($data->get('location')) : null,
                    text: $data['contents']->map(fn(Collection $t) => new Text(
                        locale: new Locale($t['locale']),
                        format: new DocumentFormat($t['format']),
                        title: $t['title'],
                        summary: $t['summary'],
                        content: $t->get('content'),
                        author: new Creator(
                            (int) $t['creator']['id'],
                            new Name(
                                first_name: $t['creator']->get('first_name'),
                                last_name: $t['creator']->get('last_name')
                            )
                        )
                    ))
                )
            ],
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
            new Training(... $props)
        );
    }
}
