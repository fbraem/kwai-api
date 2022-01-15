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
use Kwai\Modules\Users\Infrastructure\UserAbilitiesTable;
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
    public function insertAbilities(Entity $user, Collection $abilities): void
    {
        if ($abilities->count() == 0)
            return;

        $abilities = $abilities
            ->map(
                fn (Entity $ability) => collect([
                    'ability_id' => $ability->id(),
                    'user_id' => $user->id()
                ])
            )
        ;

        $query = $this->db->createQueryFactory()
            ->insert(UserAbilitiesTable::name())
            ->columns(... $abilities->first()->keys())
        ;
        $abilities->each(
            fn(Collection $ability) => $query->values(... $ability->values())
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
    public function deleteAbilities(Entity $user, Collection $abilities): void
    {
        if ($abilities->count() == 0)
            return;

        $abilityIds = $abilities->map(fn (Entity $item) => $item->id());

        $query = $this->db->createQueryFactory()
            ->delete(UserAbilitiesTable::name())
            ->where(
                UserAbilitiesTable::field('user_id')
                    ->eq($user->id())
            )->andWhere(
                UserAbilitiesTable::field('ability_id')
                    ->in(...$abilityIds->values())
            )
        ;
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }
}
