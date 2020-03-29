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
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Infrastructure\AccessTokensTable;
use Kwai\Modules\Users\Infrastructure\Mappers\UserAccountMapper;
use Kwai\Modules\Users\Infrastructure\Mappers\UserMapper;
use Kwai\Modules\Users\Infrastructure\UserAbilitiesTable;
use Kwai\Modules\Users\Infrastructure\UsersTable;
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
class UserDatabaseRepository implements UserRepository
{
    /**
     * The database connection
     */
    private Connection $db;

    /**
     * The table for 'users'
     */
    private UsersTable $table;

    /**
     * Constructor
     *
     * @param Connection $db A database object
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
        $this->table = new UsersTable();
    }

    /**
     * @inheritDoc
     * @return Entity<User>
     */
    public function getById(int $id): Entity
    {
        $query = $this->db->createQueryFactory()
            ->select(... $this->table->alias())
            ->from($this->table->from())
            ->where(field('id')->eq($id))
            ->compile();

        try {
            $user = $this->db->execute($query)->fetch();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        if ($user) {
            return UserMapper::toDomain(
                $this->table->filter($user)
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
        $query = $this->db->createQueryFactory()
            ->select(... $this->table->alias())
            ->from($this->table->from())
            ->where(field('uuid')->eq($uid))
            ->compile();

        try {
            $user = $this->db->execute($query)->fetch();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        if ($user) {
            return UserMapper::toDomain(
                $this->table->filter($user)
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
        $query = $this->db->createQueryFactory()
            ->select(... $this->table->alias())
            ->from($this->table->from())
            ->where(field('email')->eq($email))
            ->compile();

        try {
            $user = $this->db->execute($query)->fetch();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        if ($user) {
            return UserAccountMapper::toDomain(
                $this->table->filter($user)
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
        $query = $this->db->createQueryFactory()
            ->select(... $this->table->alias())
            ->from($this->table->from())
            ->where(field('email')->eq($email))
            ->compile();

        try {
            $user = $this->db->execute($query)->fetch();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        if ($user) {
            return UserMapper::toDomain(
                $this->table->filter($user)
            );
        }
        throw new NotFoundException('User');
    }

    /**
     * @inheritdoc
     */
    public function existsWithEmail(EmailAddress $email): bool
    {
        $query = $this->db->createQueryFactory()
            ->select(alias(func('COUNT', $this->table->from() . '.id'), 'c'))
            ->from($this->table->from())
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
        $accessTokenTable = new AccessTokensTable();
        $columns = array_merge(
            $this->table->alias(),
            $accessTokenTable->alias()
        );
        $query = $this->db->createQueryFactory()
            ->select(... $columns)
            ->from($this->table->from())
            ->join($accessTokenTable->from(), on('users.id', 'oauth_access_tokens.user_id'))
            ->where(field('oauth_access_tokens.identifier')->eq(strval($token)))
            ->compile();

        try {
            $data = $this->db->execute($query)->fetch();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        if ($data) {
            $userRow = $this->table->filter($data);
            $accessTokenRow = $accessTokenTable->filter($data);
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
            ->update($this->table->from(), $data)
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
            ->insert($this->table->from())
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
        return $this->db->createQueryFactory()
            ->select(... $this->table->alias())
            ->from($this->table->from())
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
        return array_map(
            fn ($row) => UserMapper::toDomain($this->table->filter($row)),
            $rows
        );
    }

    /**
     * @inheritDoc
     */
    public function addAbility(Entity $user, Entity $ability): Entity
    {
        $table = new UserAbilitiesTable();
        $query = $this->db->createQueryFactory()
            ->insert($table->from())
            ->columns(... $table->getColumns())
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
        $user->addAbility($ability);
        return $user;
    }
}
