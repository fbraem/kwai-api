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
use Kwai\Core\Infrastructure\Mappers\CreatorMapper;
use Kwai\Modules\Trainings\Domain\ValueObjects\TrainingCoach;

/**
 * Class TrainingCoachMapper
 */
class TrainingCoachMapper
{
    /**
     * Maps a table record to TrainingCoach.
     *
     * @param Collection $data
     * @return TrainingCoach
     */
    public static function toDomain(Collection $data): TrainingCoach
    {
        return new TrainingCoach(
            coach: new Entity(
                (int) $data->get('coach_id'),
                CoachMapper::toDomain($data)
            ),
            head: $data->get('coach_type', 0) === 1,
            payed: $data->get('payed', '0') === '1',
            present: $data->get('present', '0') === '1',
            remark: $data->get('remark'),
            creator: CreatorMapper::toDomain($data->get('creator')),
            traceableTime: new TraceableTime(
                Timestamp::createFromString($data->get('created_at')),
                $data->has('updated_at')
                    ? Timestamp::createFromString($data->get('updated_at'))
                    : null
            )
        );
    }

    public static function toPersistence(TrainingCoach $trainingCoach): Collection
    {
        return collect([
            'coach_id' => $trainingCoach->getCoach()->id(),
            'coach_type' => $trainingCoach->isHead() ? 1 : 0,
            'present' => $trainingCoach->isPresent(),
            'payed' => $trainingCoach->isPayed(),
            'remark' => $trainingCoach->getRemark(),
            'user_id' => $trainingCoach->getCreator()->getId(),
            'created_at' => (string) $trainingCoach->getTraceableTime()->getCreatedAt(),
            'updated_at' => $trainingCoach->getTraceableTime()->getUpdatedAt(),
        ]);
    }
}
