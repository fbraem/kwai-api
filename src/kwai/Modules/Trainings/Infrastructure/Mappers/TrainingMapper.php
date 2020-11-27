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
use Kwai\Modules\Trainings\Domain\ValueObjects\TrainingDefinition;

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
        return new Entity(
            (int) $data['id'],
            new Training((object)[
                'event' => new Event(
                    Timestamp::createFromString($data['start_date'], $data['time_zone']),
                    Timestamp::createFromString($data['end_date'], $data['time_zone']),
                    $data->has('location') ? new Location($data->get('location')) : null,
                    $data['contents']->map(fn(Collection $t) => new Text(
                        new Locale($t['locale']),
                        new DocumentFormat($t['format']),
                        $t['title'],
                        $t['summary'],
                        $t->get('content'),
                        new Creator(
                            (int) $t['creator']['id'],
                            new Name(
                                $t['creator']->get('first_name'),
                                $t['creator']->get('last_name')
                            )
                        )
                    ))
                ),
                'remark' => $data->get('remark', null),
                'coaches' => $data->get('coaches'),
                'definition' => $data->has('definition')
                    ? new TrainingDefinition(
                        (int) $data['definition']['id'],
                        $data['definition']['name']
                    )
                    : null,
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
