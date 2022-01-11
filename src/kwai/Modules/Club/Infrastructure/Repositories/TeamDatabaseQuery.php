<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Club\Infrastructure\Mappers\TeamDTO;
use Kwai\Modules\Club\Infrastructure\TeamsTable;
use Kwai\Modules\Club\Repositories\TeamQuery;

/**
 * Class TeamDatabaseQuery
 */
class TeamDatabaseQuery extends DatabaseQuery implements TeamQuery
{
    /**
     *
     */
    public function __construct(Connection $db)
    {
        parent::__construct($db);
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
        return TeamsTable::aliases();
    }

    /**
     * @inheritDoc
     */
    public function filterId(int $id): TeamQuery
    {
        $this->query->andWhere(
            TeamsTable::field('id')->eq($id)
        );
        return $this;
    }

    /**
     * @inheritDoc
     * @returns Collection<TeamDTO>
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $teams = new Collection();

        foreach ($rows as $row) {
            $team = TeamsTable::createFromRow($row);
            $teams->put($team->id, new TeamDTO($team));
        }

        return $teams;
    }
}
