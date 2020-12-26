<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
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
     * @return Training
     */
    public static function toDomain(Collection $data): Training
    {
        $definition = $data->get('definition');

        return new Training(
            event: EventMapper::toDomain($data),
            text: $data->get('contents')->map(fn ($text) => TextMapper::toDomain($text)),
            definition: $definition
                ? new Entity((int) $definition->get('id'),DefinitionMapper::toDomain($definition))
                : null,
            teams: $data->get('teams')->map(
                fn ($team) => new Entity((int) $team->get('id'), TeamMapper::toDomain($team))
            ),
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
        $data = collect([
            'definition_id' => $training->getDefinition()?->id(),
            'created_at' => (string) $training->getTraceableTime()->getCreatedAt(),
            'updated_at' => $training->getTraceableTime()->getUpdatedAt()?->__toString(),
            'remark' => $training->getRemark(),
        ]);
        return $data->merge(
            EventMapper::toPersistence($training->getEvent())
        );
    }
}
