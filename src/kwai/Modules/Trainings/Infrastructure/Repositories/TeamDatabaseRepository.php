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
use Kwai\Modules\Trainings\Infrastructure\Mappers\TeamMapper;
use Kwai\Modules\Trainings\Repositories\TeamQuery;
use Kwai\Modules\Trainings\Repositories\TeamRepository;

/**
 * Class TeamDatabaseRepository
 */
class TeamDatabaseRepository extends DatabaseRepository implements TeamRepository
{
    /**
     * TeamDatabaseRepository constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            fn($item) => TeamMapper::toDomain($item)
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
     * Factory method
     *
     * @return TeamQuery
     */
    public function createQuery(): TeamQuery
    {
        return new TeamDatabaseQuery($this->db);
    }
}
