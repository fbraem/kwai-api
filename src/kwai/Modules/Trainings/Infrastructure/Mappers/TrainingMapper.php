<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

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
     * @param object $raw
     * @return Entity<Training>
     */
    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new Training((object)[
                'event' => new Event(
                    Timestamp::createFromString($raw->start_date, $raw->time_zone),
                    Timestamp::createFromString($raw->end_date, $raw->time_zone),
                    isset($raw->location) ? new Location($raw->location) : null,
                    array_map(fn($t) => new Text(
                        new Locale($t->locale),
                        new DocumentFormat($t->format),
                        $t->title,
                        $t->summary,
                        $t->content,
                        new Creator(
                            (int) $t->creator->id,
                            new Name(
                                $t->creator->first_name,
                                $t->creator->last_name
                            )
                        )
                    ), $raw->contents)
                ),
                'remark' => $raw->remark ?? null,
                'definition' => isset($raw->definition)
                    ? new TrainingDefinition(
                        (int) $raw->definition->id,
                        $raw->definition->name
                    )
                    : null,
                'traceableTime' => new TraceableTime(
                    Timestamp::createFromString($raw->created_at),
                    isset($raw->updated_at)
                        ? Timestamp::createFromString($raw->updated_at)
                        : null
                ),
            ])
        );
    }
}
