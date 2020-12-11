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
use Kwai\Modules\Trainings\Domain\Exceptions\SeasonNotFoundException;
use Kwai\Modules\Trainings\Infrastructure\Mappers\SeasonMapper;
use Kwai\Modules\Trainings\Infrastructure\Tables;
use Kwai\Modules\Trainings\Repositories\SeasonRepository;
use function Latitude\QueryBuilder\field;

/**
 * Class SeasonDatabaseRepository
 */
class SeasonDatabaseRepository extends DatabaseRepository implements SeasonRepository
{
    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        $query = $this->db->createQueryFactory()
            ->select()
            ->from((string) Tables::SEASONS())
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
            return SeasonMapper::toDomain($row->first());
        }

        throw new SeasonNotFoundException($id);
    }

    /**
     * @inheritDoc
     */
    public function getAll(?int $limit = null, ?int $offset = null): Collection
    {
        $query = $this->db->createQueryFactory()
            ->select()
            ->from((string) Tables::SEASONS())
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
            $teams->put($row['id'], SeasonMapper::toDomain($row));
        }

        return $teams;
    }
}
