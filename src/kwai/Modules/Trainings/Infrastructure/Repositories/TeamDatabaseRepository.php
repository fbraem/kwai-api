<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Mappers\TeamMapper;
use Kwai\Modules\Trainings\Infrastructure\Tables;
use Kwai\Modules\Trainings\Repositories\TeamRepository;
use Latitude\QueryBuilder\Query\SelectQuery;
use function Latitude\QueryBuilder\field;

/**
 * Class TeamDatabaseRepository
 */
class TeamDatabaseRepository extends DatabaseRepository implements TeamRepository
{
    /**
     * @inheritDoc
     */
    public function getById(int ... $ids): Collection
    {
        $query = $this->createBaseQuery()
            ->where(field('id')->in(...$ids))
        ;

        $this->db->asArray();
        $rows = LazyCollection::make($this->db->walk($query));
        $this->db->asObject();

        return $this->createTeamCollection($rows);
    }

    /**
     * @inheritDoc
     */
    public function getAll(?int $limit = null, ?int $offset = null): Collection
    {
        $query = $this->createBaseQuery();

        $this->db->asArray();
        $rows = LazyCollection::make($this->db->walk($query));
        $this->db->asObject();

        return $this->createTeamCollection($rows);
    }

    private function createBaseQuery(): SelectQuery
    {
        return $this->db->createQueryFactory()
            ->select()
            ->from((string) Tables::TEAMS())
            ->columns(
                'id',
                'name'
            )
        ;
    }

    private function createTeamCollection(LazyCollection $rows)
    {
        $teams = new Collection();
        foreach($rows as $row) {
            $teams->put($row['id'], TeamMapper::toDomain($row));
        }
        return $teams;
    }
}
