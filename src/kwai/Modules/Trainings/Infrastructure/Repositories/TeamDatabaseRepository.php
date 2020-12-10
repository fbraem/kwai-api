<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Modules\Trainings\Domain\Exceptions\TeamNotFoundException;
use Kwai\Modules\Trainings\Infrastructure\Mappers\TeamMapper;
use Kwai\Modules\Trainings\Infrastructure\Tables;
use Kwai\Modules\Trainings\Repositories\TeamRepository;
use function Latitude\QueryBuilder\field;

/**
 * Class TeamDatabaseRepository
 */
class TeamDatabaseRepository extends DatabaseRepository implements TeamRepository
{
    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        $query = $this->db->createQueryFactory()
            ->select()
            ->from((string) Tables::TEAMS())
            ->columns(
                'id',
                'name'
            )
            ->where(field('id')->eq($id))
        ;

        $this->db->asArray();
        $row = LazyCollection::make($this->db->walk($query));
        $this->db->asObject();
        if ($row->count() > 0) {
            return TeamMapper::toDomain($row->first());
        }

        throw new TeamNotFoundException($id);
    }

    /**
     * @inheritDoc
     */
    public function getAll(?int $limit = null, ?int $offset = null): Collection
    {
        $query = $this->db->createQueryFactory()
            ->select()
            ->from((string) Tables::TEAMS())
            ->columns(
                'id',
                'name'
            )
        ;

        $this->db->asArray();
        $rows = LazyCollection::make($this->db->walk($query));
        $this->db->asObject();

        $teams = new Collection();
        foreach($rows as $row) {
            $teams->put($row['id'], TeamMapper::toDomain($row));
        }

        return $teams;
    }
}
