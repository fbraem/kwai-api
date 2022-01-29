<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Infrastructure\Mappers\UserDTO;
use Kwai\Modules\Users\Infrastructure\UserRolesTable;
use Kwai\Modules\Users\Infrastructure\UsersTable;
use Kwai\Modules\Users\Repositories\UserQuery;
use Kwai\Modules\Users\Repositories\UserRepository;
use function Latitude\QueryBuilder\field;

/**
 * Class UserDatabaseRepository
 *
 * User Repository for read/write User entity from/to a database.
 */
class UserDatabaseRepository extends DatabaseRepository implements UserRepository
{
    /**
     * UserDatabaseRepository constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            static fn(UserDTO $dto) => $dto->createEntity()
        );
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        $query = $this->createQuery()->filterById($id);

        $entities = $this->getAll($query);
        if ($entities->isEmpty()) {
            throw new UserNotFoundException($id);
        }

        return $entities->get($id);
    }

    /**
     * @inheritDoc
     */
    public function getByUniqueId(UniqueId $uuid): Entity
    {
        $query = $this->createQuery()->filterByUUID($uuid);

        $entities = $this->getAll($query);
        if ($entities->isEmpty()) {
            throw new UserNotFoundException($uuid);
        }

        return $entities->first();
    }

    public function update(Entity $user): void
    {
        $data = (new UserDTO())
            ->persistEntity($user)
            ->user
            ->collect()
        ;

        $queryFactory = $this->db->createQueryFactory();
        $query = $queryFactory
            ->update(UsersTable::name())
            ->set($data->toArray())
            ->where(field('id')->eq($user->id()))
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
    public function createQuery(): UserQuery
    {
        return new UserDatabaseQuery($this->db);
    }

    /**
     * @inheritDoc
     */
    public function insertRoles(Entity $user, Collection $roles): void
    {
        if ($roles->count() == 0)
            return;

        $roles = $roles
            ->map(
                fn (Entity $role) => collect([
                    'role_id' => $role->id(),
                    'user_id' => $user->id()
                ])
            )
        ;

        $query = $this->db->createQueryFactory()
            ->insert(UserRolesTable::name())
            ->columns(... $roles->first()->keys())
        ;
        $roles->each(
            fn(Collection $role) => $query->values(... $role->values())
        );

        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteRoles(Entity $user, Collection $roles): void
    {
        if ($roles->count() == 0)
            return;

        $roleIds = $roles->map(fn (Entity $item) => $item->id());

        $query = $this->db->createQueryFactory()
            ->delete(UserRolesTable::name())
            ->where(
                UserRolesTable::field('user_id')
                    ->eq($user->id())
            )->andWhere(
                UserRolesTable::field('role_id')
                    ->in(...$roleIds->values())
            )
        ;
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }
}
