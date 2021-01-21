<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Modules\Coaches\Domain\Exceptions\CoachNotFoundException;
use Kwai\Modules\Coaches\Infrastructure\Mappers\CoachMapper;
use Kwai\Modules\Coaches\Repositories\CoachQuery;
use Kwai\Modules\Coaches\Repositories\CoachRepository;

/**
 * Class CoachDatabaseRepository
 *
 * Database repository for the Coach entity
 */
class CoachDatabaseRepository extends DatabaseRepository implements CoachRepository
{
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            fn($item) => CoachMapper::toDomain($item)
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
    public function getById(int $id): Entity
    {
        $query = $this->createQuery()->filterIds($id);

        $coaches = $this->getAll($query);
        if ($coaches->count() == 0) {
            throw new CoachNotFoundException($id);
        }

        return $coaches->first();
    }
}
