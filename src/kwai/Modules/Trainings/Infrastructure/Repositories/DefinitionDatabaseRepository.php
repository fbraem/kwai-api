<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Definition;
use Kwai\Modules\Trainings\Domain\DefinitionEntity;
use Kwai\Modules\Trainings\Domain\Exceptions\DefinitionNotFoundException;
use Kwai\Modules\Trainings\Infrastructure\DefinitionsTable;
use Kwai\Modules\Trainings\Infrastructure\Mappers\DefinitionDTO;
use Kwai\Modules\Trainings\Infrastructure\Tables;
use Kwai\Modules\Trainings\Infrastructure\TrainingsTable;
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
            static fn(DefinitionDTO $dto) => $dto->createEntity()
        );
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): DefinitionEntity
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
    public function create(Definition $definition): DefinitionEntity
    {
        $dto = (new DefinitionDTO())->persist($definition);
        $data = $dto->definition->collect();

        $query = $this->db->createQueryFactory()
            ->insert(DefinitionsTable::name())
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

        return new DefinitionEntity(
            $this->db->lastInsertId(),
            $definition
        );
    }

    /**
     * @inheritDoc
     */
    public function update(DefinitionEntity $definition): void
    {
        $dto = (new DefinitionDTO())->persistEntity($definition);
        $data = $dto->definition
            ->collect()
            ->forget('id')
        ;

        $query = $this->db->createQueryFactory()
            ->update(DefinitionsTable::name())
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
    public function remove(DefinitionEntity $definition): void
    {
        $query = $this->db->createQueryFactory()
            ->delete(DefinitionsTable::name())
            ->where(field('id')->eq($definition->id()))
        ;
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        // Update all trainings that belonged to this definition.
        $query = $this->db->createQueryFactory()
            ->update(TrainingsTable::name())
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
