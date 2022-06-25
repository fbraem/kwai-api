<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Trainings\Infrastructure\DefinitionsTable;
use Kwai\Modules\Trainings\Infrastructure\Mappers\CreatorDTO;
use Kwai\Modules\Trainings\Infrastructure\Mappers\DefinitionDTO;
use Kwai\Modules\Trainings\Infrastructure\Mappers\SeasonDTO;
use Kwai\Modules\Trainings\Infrastructure\Mappers\TeamDTO;
use Kwai\Modules\Trainings\Infrastructure\SeasonsTable;
use Kwai\Modules\Trainings\Infrastructure\TeamsTable;
use Kwai\Modules\Trainings\Infrastructure\UsersTable;
use Kwai\Modules\Trainings\Repositories\DefinitionQuery;
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

        $definitions = new Collection();
        foreach ($rows as $row) {
            $definition = DefinitionsTable::createFromRow($row);
            $definitions->put(
                $definition->id,
                new DefinitionDTO(
                    definition: $definition,
                    season: new SeasonDTO(SeasonsTable::createFromRow($row)),
                    team: new TeamDTO(TeamsTable::createFromRow($row)),
                    creator: new CreatorDTO(UsersTable::createFromRow($row))
                )
            );
        }
        return $definitions;
    }

    /**
     * @inheritDoc
     */
    public function filterId(int $id): self
    {
        $this->query->andWhere(
            DefinitionsTable::field('id')->eq($id)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterIds(Collection $ids): self
    {
        $this->query->andWhere(
            DefinitionsTable::field('id')->in(...$ids->toArray())
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query
            ->from(DefinitionsTable::name())
            ->leftJoin(
                TeamsTable::name(),
                on(
                    TeamsTable::column('id'),
                    DefinitionsTable::column('team_id')
                )
            )
            ->leftJoin(
                SeasonsTable::name(),
                on(
                    SeasonsTable::column('id'),
                    DefinitionsTable::column('season_id')
                )
            )
            ->join(
                UsersTable::name(),
                on(
                    UsersTable::column('id'),
                    DefinitionsTable::column('user_id')
                )
            )
        ;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        return [
            ...DefinitionsTable::aliases(),
            ...TeamsTable::aliases(),
            ...SeasonsTable::aliases(),
            ...UsersTable::aliases()
        ];
    }
}
