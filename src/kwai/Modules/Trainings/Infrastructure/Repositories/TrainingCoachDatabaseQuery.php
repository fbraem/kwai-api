<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Illuminate\Support\Collection;
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
            ->join(
                (string) Tables::USERS(),
                on(Tables::USERS()->id, Tables::TRAINING_COACHES()->user_id)
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
        $userAliasFn = Tables::USERS()->getAliasFn();

        return [
            $trainingCoachAliasFn('training_id'),
            $trainingCoachAliasFn('coach_id'),
            $trainingCoachAliasFn('coach_type'),
            $trainingCoachAliasFn('present'),
            $trainingCoachAliasFn('payed'),
            $trainingCoachAliasFn('remark'),
            $trainingCoachAliasFn('created_at'),
            $trainingCoachAliasFn('updated_at'),
            $personAliasFn('firstname'),
            $personAliasFn('lastname'),
            $userAliasFn('id'),
            $userAliasFn('first_name'),
            $userAliasFn('last_name')
        ];
    }

    /**
     * Get all coaches for the given trainings
     *
     * @param int[] $ids
     * @return TrainingCoachDatabaseQuery
     */
    public function filterOnTrainings(array $ids): self
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::TRAINING_COACHES()->training_id)->in(...$ids)
        );
        return $this;
    }

    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $prefixes = new Collection([
            Tables::TRAINING_COACHES()->getAliasPrefix(),
            Tables::PERSONS()->getAliasPrefix(),
            Tables::USERS()->getAliasPrefix()
        ]);

        $trainings = new Collection();
        foreach ($rows as $row) {
            [ $trainingCoach, $person, $creator ] = $row->filterColumns($prefixes);
            $trainingCoach->put('id', $trainingCoach->get('coach_id'));
            $trainingCoach = $trainingCoach->merge($person);
            $trainingCoach->put('creator', $creator);
            if (!$trainings->has($trainingCoach['training_id'])) {
                $trainings->put($trainingCoach['training_id'], new Collection());
            }
            $trainings[$trainingCoach['training_id']]->put(
                $trainingCoach->get('id'),
                $trainingCoach
            );
        }
        return $trainings;
    }
}
