<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Club\Domain\Exceptions\TeamNotFoundException;
use Kwai\Modules\Club\Infrastructure\Mappers\TeamMapper;
use Kwai\Modules\Club\Repositories\TeamQuery;
use Kwai\Modules\Club\Repositories\TeamRepository;

/**
 * Class TeamDatabaseRepository
 */
class TeamDatabaseRepository extends DatabaseRepository implements TeamRepository
{
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
        fn ($item) => TeamMapper::toDomain($item)
        );
    }

    /**
     * @inheritDoc
     */
    public function createQuery(): TeamDatabaseQuery
    {
        return new TeamDatabaseQuery($this->db);
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        $query = $this->createQuery()->filterId($id);
        $teams = $this->getAll($query);
        if ($teams->isEmpty()) {
            throw new TeamNotFoundException($id);
        }

        return $teams->first();
    }
}
