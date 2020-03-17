<?php
/**
 * User Repository.
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Infrastructure\Database;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Infrastructure\AccessTokensTable;
use Kwai\Modules\Users\Infrastructure\Mappers\UserAccountMapper;
use Kwai\Modules\Users\Infrastructure\Mappers\UserMapper;
use Kwai\Modules\Users\Infrastructure\UsersTable;
use Kwai\Modules\Users\Repositories\UserRepository;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\func;
use function Latitude\QueryBuilder\on;

/**
 * User Repository for read/write User entity from/to a database.
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
final class UserDatabaseRepository implements UserRepository
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
     * Get the user with the given id.
     *
     * @param int $id The id of the user
     * @return Entity<User>     The user
     * @throws Database\DatabaseException
     * @throws NotFoundException When user is not found
     */
    public function getById(int $id): Entity
    {
        $query = $this->db->createQueryFactory()
            ->select(... $this->table->alias())
            ->from($this->table->from())
            ->where(field('id')->eq($id))
            ->compile()
        ;

        $user = $this->db->execute($query)->fetch();
        if ($user) {
            return UserMapper::toDomain(
                $this->table->filter($user)
            );
        }
        throw new NotFoundException('User');
    }

    /**
     * Get the user with the given uuid
     *
     * @param UniqueId $uid
     * @return Entity<User>
     * @throws Database\DatabaseException
     * @throws NotFoundException
     */
    public function getByUUID(UniqueId $uid): Entity
    {
        $query = $this->db->createQueryFactory()
            ->select(... $this->table->alias())
            ->from($this->table->from())
            ->where(field('uuid')->eq($uid))
            ->compile()
        ;

        $user = $this->db->execute($query)->fetch();
        if ($user) {
            return UserMapper::toDomain(
                $this->table->filter($user)
            );
        }
        throw new NotFoundException('User');
    }

    /**
     * Get the user account with the given email.
     *
     * @param EmailAddress $email The email address of the user
     * @return Entity<User>         The user
     * @throws Database\DatabaseException
     * @throws NotFoundException When user is not found
     */
    public function getAccount(EmailAddress $email): Entity
    {
        $query = $this->db->createQueryFactory()
            ->select(... $this->table->alias())
            ->from($this->table->from())
            ->where(field('email')->eq($email))
            ->compile()
        ;

        $user = $this->db->execute($query)->fetch();
        if ($user) {
            return UserAccountMapper::toDomain(
                $this->table->filter($user)
            );
        }
        throw new NotFoundException('User');
    }

    /**
     * Get the user with the given email.
     *
     * @param EmailAddress $email The email address of the user
     * @return Entity<User>         The user
     * @throws Database\DatabaseException
     * @throws NotFoundException When user is not found
     */
    public function getByEmail(EmailAddress $email): Entity
    {
        $query = $this->db->createQueryFactory()
            ->select(... $this->table->alias())
            ->from($this->table->from())
            ->where(field('email')->eq($email))
            ->compile()
        ;

        $user = $this->db->execute($query)->fetch();
        if ($user) {
            return UserMapper::toDomain(
                $this->table->filter($user)
            );
        }
        throw new NotFoundException('User');
    }

    /**
     * @inheritdoc
     * @throws Database\DatabaseException
     */
    public function existsWithEmail(EmailAddress $email) : bool
    {
        $query = $this->db->createQueryFactory()
            ->select(alias(func('COUNT', $this->table->from() . '.id'), 'c'))
            ->from($this->table->from())
            ->where(field('email')->eq(strval($email)))
            ->compile()
        ;
        $count = $this->db->execute($query)->fetch();
        return $count->c > 0;
    }


    /**
     * Get a user using a token
     *
     * @param TokenIdentifier $token
     * @return Entity<User>
     * @throws Database\DatabaseException
     * @throws NotFoundException When user is not found
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
            ->compile()
        ;

        $data = $this->db->execute($query)->fetch();
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
     * @throws Database\DatabaseException
     */
    public function updateAccount(Entity $account): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $account->getUser()->getTraceableTime()->markUpdated();
        $data = UserAccountMapper::toPersistence($account->domain());
        $query = $this->db->createQueryFactory()
            ->update($this->table->from(), $data)
            ->where(field('id')->eq($account->id()))
            ->compile()
        ;
        $this->db->execute($query);
    }
}
