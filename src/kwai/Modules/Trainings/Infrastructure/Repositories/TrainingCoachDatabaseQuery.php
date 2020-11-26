<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Core\Infrastructure\Database\ColumnCollection;
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
        $this->db->asArray();
        $rows = LazyCollection::make(
            parent::walk($limit, $offset)
        );
        $this->db->asObject();

        $prefixes = new Collection([
            Tables::TRAINING_COACHES()->getAliasPrefix(),
            Tables::PERSONS()->getAliasPrefix()
        ]);

        $trainings = new Collection();
        foreach ($rows as $row) {
            $columns = new ColumnCollection($row);
            [ $trainingCoach, $person ] = $columns->filter($prefixes);
            $trainingCoach['person'] = $person;
            if (!$trainings->has($trainingCoach['training_id'])) {
                $trainings->put($trainingCoach['training_id'], new Collection());
            }
            $trainings[$trainingCoach['training_id']]->push($trainingCoach);
        }

        return $trainings;
    }
}
