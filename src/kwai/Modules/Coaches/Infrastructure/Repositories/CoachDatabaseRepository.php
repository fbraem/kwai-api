<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Coaches\Domain\Coach;
use Kwai\Modules\Coaches\Domain\Exceptions\CoachNotFoundException;
use Kwai\Modules\Coaches\Infrastructure\Mappers\CoachMapper;
use Kwai\Modules\Coaches\Infrastructure\Tables;
use Kwai\Modules\Coaches\Repositories\CoachQuery;
use Kwai\Modules\Coaches\Repositories\CoachRepository;
use function Latitude\QueryBuilder\field;

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

    /**
     * @inheritDoc
     */
    public function update(Entity $coach): void
    {
        try {
            $this->db->begin();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        try {
            $data = CoachMapper::toPersistence($coach->domain());
            $queryFactory = $this->db->createQueryFactory();

            $this->db->execute(
                $queryFactory
                ->update((string) Tables::COACHES())
                ->set($data->toArray())
                ->where(field('id')->eq($coach->id()))
            );
        } catch (QueryException $qe) {
            try {
                $this->db->rollback();
            } catch (DatabaseException $e) {
                throw new RepositoryException(__METHOD__, $e);
            }
            throw new RepositoryException(__METHOD__, $qe);
        }

        try {
            $this->db->commit();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function create(Coach $coach): Entity
    {
        try {
            $this->db->begin();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        $data = CoachMapper::toPersistence($coach);

        $query = $this->db->createQueryFactory()
            ->insert((string)Tables::COACHES())
            ->columns(... $data->keys())
            ->values(... $data->values());

        try {
            $this->db->execute($query);
            $entity = new Entity(
                $this->db->lastInsertId(),
                $coach
            );
        } catch (QueryException $e) {
            try {
                $this->db->rollback();
            } catch (DatabaseException $e) {
                throw new RepositoryException(__METHOD__, $e);
            }
            throw new RepositoryException(__METHOD__, $e);
        }

        try {
            $this->db->commit();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        return $entity;
    }
}
