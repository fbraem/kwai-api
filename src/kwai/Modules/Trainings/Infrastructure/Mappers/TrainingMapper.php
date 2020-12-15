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
     * @return Training
     */
    public static function toDomain(Collection $data): Training
    {
        return new Training(
            event: EventMapper::toDomain($data),
            text: $data->get('contents')->map(fn ($text) => TextMapper::toDomain($text)),
            definition: $data->has('definition')
                ? new Entity((int) $data->get('definition')->get('id'), DefinitionMapper::toDomain($data)) : null,
            teams: $data->get('teams')->map(fn ($team) => TeamMapper::toDomain($team)),
            coaches: $data->get('coaches')->map(fn ($coach) => TrainingCoachMapper::toDomain($coach)),
            remark: $data->get('remark'),
            presences: new Collection(),
            traceableTime: new TraceableTime(
                Timestamp::createFromString($data->get('created_at')),
                $data->has('updated_at')
                    ? Timestamp::createFromString($data->get('updated_at'))
                    : null
            )
        );
    }

    /**
     * Maps a training to a table record
     *
     * @param Training $training
     * @return Collection
     */
    public static function toPersistence(Training $training): Collection
    {
        return collect([
            'definition_id' => $training->getDefinition()?->id(),
            ... EventMapper::toPersistence($training->getEvent()),
            'created_at' => (string) $training->getTraceableTime()->getCreatedAt(),
            'updated_at' => $training->getTraceableTime()->getUpdatedAt()?->__toString(),
            'remark' => $training->getRemark(),
            'coaches' => $training->getCoaches()->map(
                fn (TrainingCoach $coach) => TrainingCoachMapper::toPersistence($coach)
            ),
            'contents' => $training->getText()->map(
                fn (Text $text) => TextMapper::toPersistence($text)
            ),
            'teams' =>  $training->getTeams()->map(fn (Entity $team) =>
                collect([
                    'team_id' => $team->id()
                ])
            )
        ]);
    }
}
