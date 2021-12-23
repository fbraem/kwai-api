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
use Kwai\Modules\Club\Infrastructure\TeamsTableSchema;
use Kwai\Modules\Club\Repositories\TeamQuery;

/**
 * Class TeamDatabaseQuery
 */
class TeamDatabaseQuery extends DatabaseQuery implements TeamQuery
{
    private TeamsTableSchema $teamsTableSchema;

    /**
     *
     */
    public function __construct(Connection $db)
    {
        $this->teamsTableSchema = new TeamsTableSchema();

        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query
            ->from(TeamsTableSchema::getTableName())
        ;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        return [
            ...$this->teamsTableSchema->getAllAliases()
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterId(int $id): TeamQuery
    {
        $this->query->andWhere(
            $this->teamsTableSchema->field('id')->eq('$id')
        );
        return $this;
    }

    /**
     * @inheritDoc
     * @returns Collection<string,TeamsTableSchema>
     */

    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $teams = new Collection();

        foreach ($rows as $row) {
            $team = $this->teamsTableSchema->map($row);
            $teams->put($team->id, $team);
        }

        return $teams;
    }
}
