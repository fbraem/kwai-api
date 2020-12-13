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
use Kwai\Modules\Trainings\Domain\Team;
use Kwai\Modules\Trainings\Domain\Training;
use Kwai\Modules\Trainings\Domain\ValueObjects\TrainingCoach;

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
            'coaches' => [ 'coaches' => $item->map(fn ($coach, $key) =>
                new TrainingCoach(
                    coach: CoachMapper::toDomain($coach),
                    head: $coach->get('coach_type', 0) === 1,
                    present: $coach->get('present', false),
                    payed: $coach->get('payed', false),
                    remark: $coach->get('remark'),
                    creator: new Creator(
                    (int) $coach->get('creator')['id'],
                        new Name(
                        $coach->get('creator')['first_name'],
                        $coach->get('creator')['last_name'])
                    ),
                    traceableTime: new TraceableTime(
                        Timestamp::createFromString($coach->get('created_at')),
                        $coach->has('updated_at')
                            ? Timestamp::createFromString($coach->get('updated_at'))
                            : null
                    )
                ))
            ],
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

    /**
     * Maps a training to a table record
     *
     * @param Training $training
     * @return array<Collection>
     */
    public static function toPersistence(Training $training): array
    {
        return [
            collect([ // Training data
                'definition_id' => $training->getDefinition()?->id(),
                'start_date' => (string) $training->getEvent()->getStartDate(),
                'end_date' => (string) $training->getEvent()->getEndDate(),
                'time_zone' => $training->getEvent()->getStartDate()->getTimezone(),
                'active' => $training->getEvent()->isActive(),
                'cancelled' => $training->getEvent()->isCancelled(),
                'location' => $training->getEvent()->getLocation()?->__toString(),
                'created_at' => (string) $training->getTraceableTime()->getCreatedAt(),
                'updated_at' => $training->getTraceableTime()->getUpdatedAt()?->__toString()
            ]),
            // Event content data
            $training->getEvent()->getText()->map(fn (Text $text) =>
                collect([
                    'locale' => (string) $text->getLocale(),
                    'format' => (string) $text->getFormat(),
                    'title' => $text->getTitle(),
                    'summary' => $text->getSummary(),
                    'content' => $text->getContent(),
                    'user_id' => $text->getAuthor()->getId()
                ])
            ),
            // Coaches data
            $training->getCoaches()->map(fn (TrainingCoach $coach) =>
                collect([
                    'coach_id' => $coach->getCoach()->id(),
                    'coach_type' => $coach->isHead() ? 1 : 0,
                    'present' => $coach->isPresent(),
                    'payed' => $coach->isPayed(),
                    'remark' => $coach->getRemark(),
                    'user_id' => $coach->getCreator()->getId(),
                    'created_at' => $coach->getTraceableTime()->getCreatedAt(),
                    'updated_at' => $coach->getTraceableTime()->getUpdatedAt(),
                ])
            ),
            // Teams data
            $training->getTeams()->map(fn (Entity $team) =>
                collect([
                    'team_id' => $team->id()
                ])
            )
        ];
    }
}
