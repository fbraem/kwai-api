<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Mappers\SeasonMapper;
use Kwai\Modules\Trainings\Repositories\SeasonQuery;
use Kwai\Modules\Trainings\Repositories\SeasonRepository;

/**
 * Class SeasonDatabaseRepository
 */
class SeasonDatabaseRepository extends DatabaseRepository implements SeasonRepository
{
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            fn($item) => SeasonMapper::toDomain($item)
        );
    }

    /**
     * @inheritDoc
     */
    public function getById(int ...$ids): Collection
    {
        $query = $this->createQuery()->filterId(...$ids);
        return $this->getAll($query);
    }

    /**
     * Factory method for the query
     *
     * @return SeasonQuery
     */
    public function createQuery(): SeasonQuery
    {
        return new SeasonDatabaseQuery($this->db);
    }
}
