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
use Kwai\Modules\Trainings\Repositories\DefinitionQuery;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * Class DefinitionDatabaseQuery
 */
class DefinitionDatabaseQuery extends DatabaseQuery implements DefinitionQuery
{
    /**
     * @inheritDoc
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $filters = new Collection([
            Tables::TRAINING_DEFINITIONS()->getAliasPrefix(),
            Tables::TEAMS()->getAliasPrefix(),
            Tables::SEASONS()->getAliasPrefix(),
            Tables::USERS()->getAliasPrefix()
        ]);

        $definitions = new Collection();
        foreach ($rows as $row) {
            [ $definition, $team, $season, $creator ] =
                $row->filterColumns($filters);
            $definition->put('creator', $creator);
            if ($team->has('id')) {
                $definition->put('team', $team);
            }
            if ($season->has('id')) {
                $definition->put('season', $season);
            }
            $definitions->put($definition['id'], $definition);
        }
        return $definitions;
    }

    /**
     * @inheritDoc
     */
    public function filterId(int $id): self
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::TRAINING_DEFINITIONS()->id)->eq($id)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterIds(Collection $ids): self
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::TRAINING_DEFINITIONS()->id)->in(...$ids->toArray())
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query
            ->from((string) Tables::TRAINING_DEFINITIONS())
            ->leftJoin(
                (string) Tables::TEAMS(),
                on(Tables::TEAMS()->id, Tables::TRAINING_DEFINITIONS()->team_id)
            )
            ->leftJoin(
                (string) Tables::SEASONS(),
                on(Tables::SEASONS()->id, Tables::TRAINING_DEFINITIONS()->season_id)
            )
            ->join(
                (string) Tables::USERS(),
                on(Tables::USERS()->id, Tables::TRAINING_DEFINITIONS()->user_id)
            )
        ;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $definitionAliasFn = Tables::TRAINING_DEFINITIONS()->getAliasFn();
        $teamAliasFn = Tables::TEAMS()->getAliasFn();
        $seasonAliasFn = Tables::SEASONS()->getAliasFn();
        $creatorAliasFn = Tables::USERS()->getAliasFn();

        return [
            $definitionAliasFn('id'),
            $definitionAliasFn('name'),
            $definitionAliasFn('description'),
            $definitionAliasFn('season_id'),
            $definitionAliasFn('team_id'),
            $definitionAliasFn('weekday'),
            $definitionAliasFn('start_time'),
            $definitionAliasFn('end_time'),
            $definitionAliasFn('time_zone'),
            $definitionAliasFn('active'),
            $definitionAliasFn('location'),
            $definitionAliasFn('remark'),
            $definitionAliasFn('user_id'),
            $definitionAliasFn('created_at'),
            $definitionAliasFn('updated_at'),
            $teamAliasFn('id'),
            $teamAliasFn('name'),
            $seasonAliasFn('id'),
            $seasonAliasFn('name'),
            $creatorAliasFn('id'),
            $creatorAliasFn('first_name'),
            $creatorAliasFn('last_name')
        ];
    }
}
