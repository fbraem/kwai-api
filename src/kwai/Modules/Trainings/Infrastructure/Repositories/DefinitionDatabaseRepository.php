<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Definition;
use Kwai\Modules\Trainings\Domain\Exceptions\DefinitionNotFoundException;
use Kwai\Modules\Trainings\Infrastructure\Mappers\DefinitionMapper;
use Kwai\Modules\Trainings\Infrastructure\Tables;
use Kwai\Modules\Trainings\Repositories\DefinitionQuery;
use Kwai\Modules\Trainings\Repositories\DefinitionRepository;
use function Latitude\QueryBuilder\field;

/**
 * Class DefinitionDatabaseRepository
 */
class DefinitionDatabaseRepository extends DatabaseRepository implements DefinitionRepository
{
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            fn($item) => DefinitionMapper::toDomain($item)
        );
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        $query = $this->createQuery()->filterId($id);

        $entities = $this->getAll($query);
        if ($entities->count() > 0) {
            return $entities->first();
        }

        throw new DefinitionNotFoundException($id);
    }

    /**
     * @inheritDoc
     */
    public function createQuery(): DefinitionQuery
    {
        return new DefinitionDatabaseQuery($this->db);
    }

    /**
     * @inheritDoc
     */
    public function create(Definition $definition): Entity
    {
        $data = DefinitionMapper::toPersistence($definition);
        $creator = $data->pull('creator');
        $data->put('user_id', $creator->get('id'));

        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::TRAINING_DEFINITIONS())
            ->columns(
                ... $data->keys()
            )
            ->values(
                ... $data->values()
            )
        ;

        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        return new Entity(
            $this->db->lastInsertId(),
            $definition
        );
    }

    /**
     * @inheritDoc
     */
    public function update(Entity $definition): void
    {
        $data = DefinitionMapper::toPersistence($definition->domain());
        $creator = $data->pull('creator');
        $data->put('user_id', $creator->get('id'));

        $query = $this->db->createQueryFactory()
            ->update((string) Tables::TRAINING_DEFINITIONS())
            ->set($data->toArray())
            ->where(field('id')->eq($definition->id()))
        ;

        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function remove(Entity $definition): void
    {
        $query = $this->db->createQueryFactory()
            ->delete((string) Tables::TRAINING_DEFINITIONS())
            ->where(field('id')->eq($definition->id()))
        ;
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        // Update all trainings that belonged to this definition.
        $query = $this->db->createQueryFactory()
            ->update((string) Tables::TRAININGS())
            ->set(
                ['definition_id' => null]
            )
            ->where(field('definition_id')->eq($definition->id()))
        ;
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }
}
