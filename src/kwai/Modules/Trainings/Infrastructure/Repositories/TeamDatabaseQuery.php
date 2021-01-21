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
        $this->query->from((string) Tables::TEAMS());
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $aliasFn = Tables::TEAMS()->getAliasFn();
        return [
            $aliasFn('id'),
            $aliasFn('name')
        ];
    }

    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $prefixes = [ Tables::TEAMS()->getAliasPrefix() ];

        $teams = new Collection();
        foreach ($rows as $row) {
            [ $team ] = $row->filterColumns($prefixes);
            $teams->put($team->get('id'), $team);
        }

        return $teams;
    }
}
