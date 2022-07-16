<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Trainings\Infrastructure\Mappers\TeamDTO;
use Kwai\Modules\Trainings\Infrastructure\TeamsTable;
use Kwai\Modules\Trainings\Infrastructure\TrainingTeamsTable;
use function Latitude\QueryBuilder\on;

/**
 * Class TrainingTeamDatabaseQuery
 *
 * Query class for querying teams of a training.
 */
class TrainingTeamDatabaseQuery extends DatabaseQuery
{
    protected function initQuery(): void
    {
        $this->query
            ->from(TrainingTeamsTable::name())
            ->join(
                TeamsTable::name(),
                on(
                    TrainingTeamsTable::column('team_id'),
                    TeamsTable::column('id')
                )
            )
        ;
    }

    /**
     * Get all teams for the given trainings
     *
     * @param int[] $ids
     */
    public function filterOnTrainings(array $ids)
    {
        $this->query->andWhere(
            TrainingTeamsTable::field('training_id')->in(...$ids)
        );
    }

    protected function getColumns(): array
    {
        return [
            ... TrainingTeamsTable::aliases(),
            ... TeamsTable::aliases()
        ];
    }

    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $trainings = new Collection();
        foreach ($rows as $row) {
            $trainingTeam = TrainingTeamsTable::createFromRow($row);
            if (!$trainings->has($trainingTeam->training_id)) {
                $trainings->put($trainingTeam->training_id, new Collection());
            }
            $trainings[$trainingTeam->training_id]->push(
                new TeamDTO(TeamsTable::createFromRow($row))
            );
        }

        return $trainings;
    }
}
