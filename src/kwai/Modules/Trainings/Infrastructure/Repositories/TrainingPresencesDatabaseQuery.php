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
 * Class TrainingPresencesDatabaseQuery
 *
 * Query class for querying presences of a training.
 */
class TrainingPresencesDatabaseQuery extends DatabaseQuery
{
    protected function initQuery(): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query
            ->from((string) Tables::TRAINING_PRESENCES())
            ->join(
                (string) Tables::MEMBERS(),
                on(Tables::TRAINING_PRESENCES()->member_id, Tables::MEMBERS()->id)
            )
            ->join(
                (string) Tables::PERSONS(),
                on(Tables::MEMBERS()->person_id, Tables::PERSONS()->id)
            )
            ->join(
                (string) Tables::USERS(),
                on(Tables::USERS()->id, Tables::TRAINING_PRESENCES()->user_id)
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
            field(Tables::TRAINING_PRESENCES()->training_id)->in(...$ids)
        );
    }

    protected function getColumns(): array
    {
        $trainingPresencesAliasFn = Tables::TRAINING_PRESENCES()->getAliasFn();
        $personAliasFn = Tables::PERSONS()->getAliasFn();
        $userAliasFn = Tables::USERS()->getAliasFn();

        return [
            $trainingPresencesAliasFn('training_id'),
            $trainingPresencesAliasFn('remark'),
            $trainingPresencesAliasFn('created_at'),
            $trainingPresencesAliasFn('updated_at'),
            $personAliasFn('firstname'),
            $personAliasFn('lastname'),
            $userAliasFn('id'),
            $userAliasFn('first_name'),
            $userAliasFn('last_name')
        ];
    }

    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $prefixes = new Collection([
            Tables::TRAINING_PRESENCES()->getAliasPrefix(),
            Tables::PERSONS()->getAliasPrefix(),
            Tables::USERS()->getAliasPrefix()
        ]);

        $trainings = new Collection();
        foreach ($rows as $row) {
            [ $presence, $person, $creator ] = $row->filterColumns($prefixes);
            $presence->put('person', $person);
            $presence->put('creator', $creator);
            if (!$trainings->has($presence['training_id'])) {
                $trainings->put($presence['training_id'], new Collection());
            }
            $trainings->get($presence['training_id'])->put(
                $presence->get('member_id'),
                $presence
            );
        }
        return $trainings;
    }
}
