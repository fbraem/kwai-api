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
    public function getById(int ...$ids): Collection
    {
        $query = $this->db->createQueryFactory()
            ->select()
            ->from((string) Tables::SEASONS())
            ->columns(
                'id',
                'name'
            )
            ->where(field('id')->in(...$ids))
        ;

        $this->db->asArray();
        $rows = LazyCollection::make($this->db->walk($query));
        $this->db->asObject();

        return $this->createSeasonCollection($rows);
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

        return $this->createSeasonCollection($rows);
    }

    /**
     * Create a collection with Season entities
     *
     * @param LazyCollection $rows
     * @return Collection
     */
    private function createSeasonCollection(LazyCollection $rows): Collection
    {
        return $rows->mapWithKeys(fn ($data) => [
            (int) $data->get('id') => new Entity((int) $data->get('id'), SeasonMapper::toDomain($data))
        ])->collect();
    }
}
