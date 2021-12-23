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
use Kwai\Modules\Trainings\Repositories\SeasonQuery;
use function Latitude\QueryBuilder\field;

/**
 * Class SeasonDatabaseQuery
 */
class SeasonDatabaseQuery extends DatabaseQuery implements SeasonQuery
{
    /**
     * @inheritDoc
     */
    public function filterId(int ...$id): SeasonQuery
    {
        $this->query->where(field('id')->in(...$id));
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query->from((string) Tables::SEASONS());
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $aliasFn = Tables::SEASONS()->getAliasFn();
        return [
            $aliasFn('id'),
            $aliasFn('name')
        ];
    }

    /**
     * @inheritDoc
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $prefixes = [ Tables::SEASONS()->getAliasPrefix() ];

        $seasons = new Collection();
        foreach ($rows as $row) {
            [ $season ] = $row->filterColumns($prefixes);
            $seasons->put($season->get('id'), $season);
        }

        return $seasons;
    }
}
