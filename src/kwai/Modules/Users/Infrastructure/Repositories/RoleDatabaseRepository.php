<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\RoleNotFoundException;
use Kwai\Modules\Users\Domain\Role;
use Kwai\Modules\Users\Infrastructure\RolesTable;
use Kwai\Modules\Users\Infrastructure\RoleRulesTable;
use Kwai\Modules\Users\Infrastructure\Mappers\RoleDTO;
use Kwai\Modules\Users\Infrastructure\Mappers\RuleDTO;
use Kwai\Modules\Users\Repositories\RoleQuery;
use Kwai\Modules\Users\Repositories\RoleRepository;
use function Latitude\QueryBuilder\field;

/**
 * Class RoleDatabaseRepository
 *
 * Role repository for read/write Role entity from/to a database.
 */
final class RoleDatabaseRepository extends DatabaseRepository implements RoleRepository
{
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            static fn(RoleDTO $dto) => $dto->createEntity()
        );
    }

    /**
     * @inheritDoc
     * @return Entity<Role>
     */
    public function getById(int $id): Entity
    {
        $query = $this->createQuery()->filterByIds($id);

        $entities = $this->getAll($query);
        if ($entities->isNotEmpty()) {
            return $entities->get($id);
        }

        throw new RoleNotFoundException($id);
    }

    /**
     * @inheritDoc
     */
    public function create(Role $role): Entity
    {
        $dto = (new RoleDTO())->persist($role);

        try {
             $this->db->begin();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        $data = $dto->role
            ->collect()
            ->forget('id')
        ;
        $query = $this->db->createQueryFactory()
            ->insert(RolesTable::name())
            ->columns(... $data->keys())
            ->values(...$data->values())
        ;

        try {
            $this->db->execute($query);

            $entity = new Entity(
                $this->db->lastInsertId(),
                $role
            );

            $this->insertRules($entity->id(), $dto->rules);
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

    /**
     * Insert rules of the role
     *
     * @param int                 $roleId
     * @param Collection<RuleDTO> $rules
     * @throws QueryException
     */
    private function insertRules(int $roleId, Collection $rules)
    {
        if ($rules->count() === 0) {
            return;
        }

        $rules
            ->transform(
                fn (RuleDTO $dto) => collect(['rule_id' => $dto->rule->id])
            )
            ->map(
                fn (Collection $item) => $item->put('role_id', $roleId)
            )
        ;
        $query = $this->db->createQueryFactory()
            ->insert(RoleRulesTable::name())
            ->columns(...$rules->first()->keys())
        ;
        $rules->each(
            fn (Collection $rule) => $query->values(... $rule->values())
        );

        $this->db->execute($query);
    }

    /**
     * @inheritDoc
     */
    public function update(Entity $role): void
    {
        try {
            $this->db->begin();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        $dto = (new RoleDTO())->persist($role->domain());
        $data = $dto->role
            ->collect()
            ->forget('id')
        ;

        $queryFactory = $this->db->createQueryFactory();
        $query = $queryFactory
            ->update(RolesTable::name())
            ->set($data->toArray())
            ->where(field('id')->eq($role->id()))
        ;
        try {
            $this->db->execute($query);
            // Update rules
            // First delete all rules
            $this->db->execute(
                $queryFactory
                  ->delete(RoleRulesTable::name())
                  ->where(field('role_id')->eq($role->id()))
            );
            // Insert the rules again
            $this->insertRules($role->id(), $dto->rules);
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
    }

    /**
     * @inheritDoc
     */
    public function createQuery(): RoleQuery
    {
        return new RoleDatabaseQuery($this->db);
    }
}
