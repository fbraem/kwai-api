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
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Infrastructure\Mappers\UserMapper;
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\UserQuery;
use Kwai\Modules\Users\Repositories\UserRepository;
use function Latitude\QueryBuilder\field;

/**
 * Class UserDatabaseRepository
 *
 * User Repository for read/write User entity from/to a database.
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class UserDatabaseRepository extends DatabaseRepository implements UserRepository
{
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

    /**
     * @inheritDoc
     */
    public function getAll(
        ?UserQuery $query = null,
        ?int $limit = null,
        ?int $offset = null
    ): Collection {
        $query ??= $this->createQuery();

        /* @var Collection $users */
        try {
            $users = $query->execute($limit, $offset);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        return $users->mapWithKeys(
            fn($item, $key) => [
                $key => new Entity((int) $key, UserMapper::toDomain($item))
            ]
        );
    }

    public function update(Entity $user): void
    {
        try {
            $this->db->begin();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        $queryFactory = $this->db->createQueryFactory();
        $query = $queryFactory
            ->update((string) Tables::USERS())
            ->set(UserMapper::toPersistence($user->domain())->toArray())
            ->where(field('id')->eq($user->id()))
        ;

        try {
            $this->db->execute($query);
            // Update abilities
            // First delete all abilities
            $this->db->execute(
                $queryFactory
                    ->delete((string) Tables::USER_ABILITIES())
                ->where(field('user_id')->eq($user->id()))
            );
            $this->insertAbilities($user);
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
    public function createQuery(): UserQuery
    {
        return new UserDatabaseQuery($this->db);
    }

    /**
     * Insert abilities of the user
     *
     * @param Entity $user
     * @throws QueryException
     */
    private function insertAbilities(Entity $user)
    {
        /* @var Collection $abilities */
        /** @noinspection PhpUndefinedMethodInspection */
        $abilities = $user->getAbilities();
        if ($abilities->count() === 0) {
            return;
        }

        $abilities
            ->transform(
                fn (Entity $ability) => collect(['ability_id' => $ability->id()])
            )
            ->map(
                fn (Collection $item) => $item->put('user_id', $user->id())
            )
        ;

        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::USER_ABILITIES())
            ->columns(... $abilities->first()->keys())
        ;
        $abilities->each(
            fn(Collection $ability) => $query->values(... $ability->values())
        );

        $this->db->execute($query);
    }
}
