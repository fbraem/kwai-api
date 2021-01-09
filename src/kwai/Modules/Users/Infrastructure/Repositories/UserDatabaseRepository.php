<?php
/**
 * @package    kwai
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Infrastructure\Mappers\UserMapper;
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\UserQuery;
use Kwai\Modules\Users\Repositories\UserRepository;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\func;

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
     * @return Entity<User>
     */
    public function getById(int $id): Entity
    {
        $query = $this->createQuery()->filterById($id);

        $entities = $this->getAll($query);
        if ($entities->isNotEmpty()) {
            return $entities->get($id);
        }

        throw new UserNotFoundException($id);
    }

    /**
     * @inheritdoc
     */
    public function existsWithEmail(EmailAddress $email): bool
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $query = $this->db->createQueryFactory()
            ->select(
                alias(
                    func('COUNT', Tables::USERS()->id),
                    'c'
                )
            )
            ->from((string) Tables::USERS())
            ->where(field('email')->eq(strval($email)))
        ;
        try {
            $count = collect($this->db->execute($query)->fetch());
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        return $count->get('c') > 0;
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

    /**
     * @inheritDoc
     */
    public function addAbility(Entity $user, Entity $ability): Entity
    {
        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::USER_ABILITIES())
            ->columns(
                'user_id',
                'ability_id',
                'created_at',
                'updated_at'
            )
            ->values(
                $user->id(),
                $ability->id(),
                strval(Timestamp::createNow()),
                null
            )
        ;
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        /** @noinspection PhpUndefinedMethodInspection */
        $user->addAbility($ability);
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function removeAbility(Entity $user, Entity $ability): Entity
    {
        $query = $this->db->createQueryFactory()
            ->delete((string) Tables::USER_ABILITIES())
            ->where(field('user_id')->eq($user->id()))
            ->andWhere(field('ability_id')->eq($ability->id()))
        ;
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        /** @noinspection PhpUndefinedMethodInspection */
        $user->removeAbility($ability);
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function createQuery(): UserQuery
    {
        return new UserDatabaseQuery($this->db);
    }
}
