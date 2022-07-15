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
use Kwai\Modules\Trainings\Repositories\TeamQuery;
use function Latitude\QueryBuilder\field;

/**
 * Class TeamDatabaseQuery
 */
class TeamDatabaseQuery extends DatabaseQuery implements TeamQuery
{
    /**
     * @inheritDoc
     */
    public function filterId(int ...$id): TeamQuery
    {
        $this->query->where(field('id')->in(...$id));
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query->from(TeamsTable::name());
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        return [
            ...TeamsTable::aliases()
        ];
    }

    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $teams = new Collection();
        foreach ($rows as $row) {
            $team = TeamsTable::createFromRow($row);
            $teams->put(
                $team->id,
                new TeamDTO($team)
            );
        }

        return $teams;
    }
}
