<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Modules\Trainings\Infrastructure\CoachesTable;
use Kwai\Modules\Trainings\Infrastructure\Mappers\TrainingCoachDTO;
use Kwai\Modules\Trainings\Infrastructure\MembersTable;
use Kwai\Modules\Trainings\Infrastructure\PersonsTable;
use Kwai\Modules\Trainings\Infrastructure\TrainingCoachesTable;
use Kwai\Modules\Trainings\Infrastructure\UsersTable;
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
        $this->query
            ->from(TrainingCoachesTable::name())
            ->join(
                CoachesTable::name(),
                on(
                    TrainingCoachesTable::column('coach_id'),
                    CoachesTable::column('id')
                )
            )
            ->join(
                MembersTable::name(),
                on(
                    CoachesTable::column('member_id'),
                    MembersTable::column('id')
                )
            )
            ->join(
                PersonsTable::name(),
                on(
                    MembersTable::column('person_id'),
                    PersonsTable::column('id')
                )
            )
            ->join(
                UsersTable::name(),
                on(
                    UsersTable::column('id'),
                    TrainingCoachesTable::column('user_id')
                )
            )
            ->orderBy(TrainingCoachesTable::column('training_id'))
        ;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        return [
            ...TrainingCoachesTable::aliases(),
            ...MembersTable::aliases(),
            ...PersonsTable::aliases(),
            ...UsersTable::aliases()
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
        $this->query->andWhere(
            TrainingCoachesTable::field('training_id')->in(...$ids)
        );
        return $this;
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return Collection<TrainingCoachDTO>
     * @throws QueryException
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $trainings = new Collection();
        foreach ($rows as $row) {
            $trainingCoach = TrainingCoachesTable::createFromRow($row);

            if (!$trainings->has($trainingCoach->training_id)) {
                $trainings->put($trainingCoach->training_id, new Collection());
            }
            $trainings[$trainingCoach->training_id]->put(
                $trainingCoach->coach_id,
                new TrainingCoachDTO(
                    trainingCoach: $trainingCoach,
                    member: MembersTable::createFromRow($row),
                    person: PersonsTable::createFromRow($row),
                    user: UsersTable::createFromRow($row)
                )
            );
        }
        return $trainings;
    }
}
