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
use Kwai\Modules\Trainings\Infrastructure\Mappers\CoachDTO;
use Kwai\Modules\Trainings\Repositories\CoachQuery;
use Kwai\Modules\Trainings\Repositories\CoachRepository;

/**
 * Class CoachDatabaseRepository
 *
 * Database repository for the Coach entity
 */
class CoachDatabaseRepository extends DatabaseRepository implements CoachRepository
{
    /**
     * CoachDatabaseRepository constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            static fn(CoachDTO $dto) => $dto->createEntity()
        );
    }

    /**
     * @inheritDoc
     */
    public function createQuery(): CoachQuery
    {
        return new CoachDatabaseQuery($this->db);
    }

    /**
     * @inheritDoc
     */
    public function getById(int ...$ids): Collection
    {
        $query = $this->createQuery()->filterId(...$ids);
        return $this->getAll($query);
    }
}
