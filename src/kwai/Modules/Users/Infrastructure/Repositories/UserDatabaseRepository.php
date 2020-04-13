<?php
/**
 * @package    kwai
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\Timestamp;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Infrastructure\Mappers\UserAccountMapper;
use Kwai\Modules\Users\Infrastructure\Mappers\UserMapper;
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\UserRepository;
use Latitude\QueryBuilder\Query\SelectQuery;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\func;
use function Latitude\QueryBuilder\on;

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
        $query = $this->createBaseQuery()
            ->where(field('id')->eq($id))
            ->compile()
        ;

        try {
            $user = $this->db->execute($query)->fetch();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        if ($user) {
            return UserMapper::toDomain(
                Tables::USERS()->createColumnFilter()->filter($user)
            );
        }
        throw new NotFoundException('User');
    }

    /**
     * @inheritDoc
     * @return Entity<User>
     */
    public function getByUUID(UniqueId $uid): Entity
    {
        $query = $this->createBaseQuery()
            ->where(field('uuid')->eq($uid))
            ->compile()
        ;

        try {
            $user = $this->db->execute($query)->fetch();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        if ($user) {
            return UserMapper::toDomain(
                Tables::USERS()->createColumnFilter()->filter($user)
            );
        }
        throw new NotFoundException('User');
    }

    /**
     * @inheritDoc
     * @return Entity<User>
     */
    public function getAccount(EmailAddress $email): Entity
    {
        $query = $this->createBaseQuery()
            ->where(field('email')->eq($email))
            ->compile()
        ;

        try {
            $user = $this->db->execute($query)->fetch();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        if ($user) {
            return UserAccountMapper::toDomain(
                Tables::USERS()->createColumnFilter()->filter($user)
            );
        }
        throw new NotFoundException('User');
    }

    /**
     * @inheritDoc
     * @return Entity<User>
     */
    public function getByEmail(EmailAddress $email): Entity
    {
        $query = $this->createBaseQuery()
            ->where(field('email')->eq($email))
            ->compile()
        ;

        try {
            $user = $this->db->execute($query)->fetch();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        if ($user) {
            return UserMapper::toDomain(
                Tables::USERS()->createColumnFilter()->filter($user)
            );
        }
        throw new NotFoundException('User');
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
            ->compile();
        try {
            $count = $this->db->execute($query)->fetch();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        return $count->c > 0;
    }


    /**
     * @inheritDoc
     * @return Entity<User>
     */
    public function getByAccessToken(TokenIdentifier $token): Entity
    {
        $aliasAccessTokenFn = Tables::ACCESS_TOKENS()->getAliasFn();
        $query = $this->createBaseQuery();
        /** @noinspection PhpUndefinedFieldInspection */
        $query = $query
            ->addColumns(
                $aliasAccessTokenFn('id'),
                $aliasAccessTokenFn('identifier'),
                $aliasAccessTokenFn('expiration'),
                $aliasAccessTokenFn('revoked'),
                $aliasAccessTokenFn('created_at'),
                $aliasAccessTokenFn('updated_at')
            )
            ->join(
                (string) Tables::ACCESS_TOKENS(),
                on(
                    Tables::USERS()->id,
                    Tables::ACCESS_TOKENS()->user_id
                )
            )
            ->where(field(Tables::ACCESS_TOKENS()->identifier)->eq(strval($token)))
            ->compile()
        ;

        try {
            $data = $this->db->execute($query)->fetch();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        if ($data) {
            $userRow = Tables::USERS()->createColumnFilter()->filter($data);
            $accessTokenRow = Tables::ACCESS_TOKENS()->createColumnFilter()->filter($data);
            $accessTokenRow->user = $userRow;

            return UserMapper::toDomain($userRow);
        }
        throw new NotFoundException('User');
    }

    /**
     * @inheritdoc
     */
    public function updateAccount(Entity $account): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $account->getUser()->getTraceableTime()->markUpdated();
        $data = UserAccountMapper::toPersistence($account->domain());
        $query = $this->db->createQueryFactory()
            ->update((string) Tables::USERS(), $data)
            ->where(field('id')->eq($account->id()))
            ->compile();
        try {
            $this->db->execute($query);
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function create(UserAccount $account): Entity
    {
        $data = UserAccountMapper::toPersistence($account);

        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::USERS())
            ->columns(
                ... array_keys($data)
            )
            ->values(
                ... array_values($data)
            )
            ->compile();
        try {
            $this->db->execute($query);
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        return new Entity(
            $this->db->lastInsertId(),
            $account
        );
    }

    /**
     * Create the base query for SELECT
     *
     * @return SelectQuery
     */
    private function createBaseQuery(): SelectQuery
    {
        $aliasFn = Tables::USERS()->getAliasFn();
        return $this->db->createQueryFactory()
            ->select(
                $aliasFn('id'),
                $aliasFn('email'),
                $aliasFn('password'),
                $aliasFn('last_login'),
                $aliasFn('first_name'),
                $aliasFn('last_name'),
                $aliasFn('remark'),
                $aliasFn('uuid'),
                $aliasFn('created_at'),
                $aliasFn('updated_at')
            )
            ->from((string) Tables::USERS())
        ;
    }

    /**
     * @inheritDoc
     * @return Entity[]
     */
    public function getAll(): array
    {
        $query = $this->createBaseQuery()->compile();
        try {
            $rows = $this->db->execute($query)->fetchAll();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        $columnFilter = Tables::USERS()->createColumnFilter();
        return array_map(
            fn ($row) => UserMapper::toDomain($columnFilter->filter($row)),
            $rows
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
            ->compile()
        ;
        try {
            $this->db->execute($query);
        } catch (DatabaseException $e) {
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
            ->compile()
        ;
        try {
            $this->db->execute($query);
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        /** @noinspection PhpUndefinedMethodInspection */
        $user->removeAbility($ability);
        return $user;
    }
}
