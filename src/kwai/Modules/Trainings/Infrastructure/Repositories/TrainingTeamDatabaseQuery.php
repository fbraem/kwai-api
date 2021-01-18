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
 * Class TrainingTeamDatabaseQuery
 *
 * Query class for querying teams of a training.
 */
class TrainingTeamDatabaseQuery extends DatabaseQuery
{
    protected function initQuery(): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query
            ->from((string) Tables::TRAINING_TEAMS())
            ->join(
                (string) Tables::TEAMS(),
                on(Tables::TRAINING_TEAMS()->team_id, Tables::TEAMS()->id)
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
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::TRAINING_TEAMS()->training_id)->in(...$ids)
        );
    }

    protected function getColumns(): array
    {
        $trainingTeamsAliasFn = Tables::TRAINING_TEAMS()->getAliasFn();
        $teamAliasFn = Tables::TEAMS()->getAliasFn();

        return [
            $trainingTeamsAliasFn('training_id'),
            $teamAliasFn('id'),
            $teamAliasFn('name')
        ];
    }

    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $prefixes = new Collection([
            Tables::TRAINING_TEAMS()->getAliasPrefix(),
            Tables::TEAMS()->getAliasPrefix()
        ]);

        $trainings = new Collection();
        foreach ($rows as $row) {
            [ $training, $team ] = $row->filterColumns($prefixes);
            if (!$trainings->has($training['training_id'])) {
                $trainings->put($training['training_id'], new Collection());
            }
            $trainings[$training['training_id']]->push($team);
        }

        return $trainings;
    }
}
