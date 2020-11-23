<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Trainings\Infrastructure\Tables;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * Class TrainingCoachDatabaseQuery
 */
class TrainingCoachDatabaseQuery extends DatabaseQuery
{
    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query
            ->from((string) Tables::TRAINING_COACHES())
            ->join(
                (string) Tables::COACHES(),
                on(Tables::TRAINING_COACHES()->coach_id, Tables::COACHES()->id)
            )
            ->join(
                (string) Tables::MEMBERS(),
                on(Tables::COACHES()->member_id, Tables::MEMBERS()->id)
            )
            ->join(
                (string) Tables::PERSONS(),
                on(Tables::MEMBERS()->person_id, Tables::PERSONS()->id)
            )
            ->orderBy(Tables::TRAINING_COACHES()->getAlias('training_id'))
        ;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $trainingCoachAliasFn = Tables::TRAINING_COACHES()->getAliasFn();
        $personAliasFn = Tables::PERSONS()->getAliasFn();

        return [
            $trainingCoachAliasFn('training_id'),
            $trainingCoachAliasFn('coach_id'),
            $trainingCoachAliasFn('coach_type'),
            $trainingCoachAliasFn('present'),
            $trainingCoachAliasFn('payed'),
            $trainingCoachAliasFn('remark'),
            $trainingCoachAliasFn('user_id'),
            $trainingCoachAliasFn('created_at'),
            $trainingCoachAliasFn('updated_at'),
            $personAliasFn('firstname'),
            $personAliasFn('lastname')
        ];
    }

    /**
     * Get all coaches for the given trainings
     *
     * @param int[] $ids
     */
    public function filterOnTrainings(array $ids)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::TRAINING_COACHES()->training_id)->in(...$ids)
        );
    }

    public function execute(?int $limit = null, ?int $offset = null)
    {
        $trainingCoachColumnFilter = Tables::TRAINING_COACHES()->createColumnFilter();
        $personColumnFilter = Tables::PERSONS()->createColumnFilter();

        $trainings = [];
        $rows = parent::execute($limit, $offset);
        foreach ($rows as $row) {
            $trainingCoach = $trainingCoachColumnFilter->filter($row);
            $trainingCoach->person = $personColumnFilter->filter($row);
            if (isset($trainings[$trainingCoach->training_id])) {
                $trainings[$trainingCoach->training_id] = [];
            }
            $trainings[$trainingCoach->training_id][] = $trainingCoach;
        }

        return $trainings;
    }
}
