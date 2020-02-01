<?php
/**
 * User Repository.
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;

use Kwai\Core\Infrastructure\TableData;
use Kwai\Core\Infrastructure\AliasTable;
use Kwai\Core\Infrastructure\Database;

use Kwai\Modules\Users\Repositories\UserRepository;
use Kwai\Modules\Users\Infrastructure\Mappers\UserMapper;
use Kwai\Modules\Users\Infrastructure\Mappers\AccessTokenMapper;
use Kwai\Modules\Users\Infrastructure\UserTable;
use Kwai\Modules\Users\Infrastructure\AccessTokenTable;

use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Core\Domain\EmailAddress;

use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\on;

/**
* User Repository for read/write User entity from/to a database.
*/
final class UserDatabaseRepository implements UserRepository
{
    /**
     * @var Database
     */
    private $db;

    private $table;

    /**
     * Constructor
     *
     * @param Database $db A database object
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->table = new UserTable();
    }

    private static function getColumns(): array
    {
        return [
            alias('users.id', 'users_id'),
            alias('users.email', 'users_email'),
            alias('users.password', 'users_password'),
            alias('users.last_login', 'users_last_login'),
            alias('users.first_name', 'users_first_name'),
            alias('users.last_name', 'users_last_name'),
            alias('users.remark', 'users_remark'),
            alias('users.uuid', 'users_uuid'),
            alias('users.created_at', 'users_created_at'),
            alias('users.updated_at', 'users_updated_at')
        ];
    }

    /**
     * Get the user with the given id.
     * @param  int  $id         The id of the user
     * @return Entity<User>     The user
     * @throws NotFoundException When user is not found
     */
    public function getById(int $id): Entity
    {
        $query = $this->db->createQueryFactory()
            ->select(... $this->getColumns())
            ->from('users')
            ->where(field('id')->eq($id))
            ->compile()
        ;

        $user = $this->db->execute($query)->fetch();
        if ($user) {
            return UserMapper::toDomain(
                new TableData($user, $this->table->prefix())
            );
        }
        throw new NotFoundException('User');
    }

    /**
     * Get the user with the given uuid
     * @param  UniqueId $uid
     * @return Entity<User>
     */
    public function getByUUID(UniqueId $uid): Entity
    {
        $query = $this->db->createQueryFactory()
            ->select(... $this->getColumns())
            ->from('users')
            ->where(field('uuid')->eq($uid))
            ->compile()
        ;

        $user = $this->db->execute($query)->fetch();
        if ($user) {
            return UserMapper::toDomain(
                new TableData($user, 'users_')
            );
        }
        throw new NotFoundException('User');
    }

    /**
     * Get the user with the given email.
     * @param  EmailAddress  $email The email address of the user
     * @return Entity<User>         The user
     * @throws NotFoundException    When user is not found
     */
    public function getByEmail(EmailAddress $email): Entity
    {
        $query = $this->db->createQueryFactory()
            ->select(... $this->getColumns())
            ->from('users')
            ->where(field('email')->eq($email))
            ->compile()
        ;

        $user = $this->db->execute($query)->fetch();
        if ($user) {
            return UserMapper::toDomain(
                new TableData($user, 'users_')
            );
        }
        throw new NotFoundException('User');
    }

    /**
     * Get a user using a token
     * @param  TokenIdentifier $token
     * @return Entity<User>
     * @throws NotFoundException    When user is not found
     */
    public function getByAccessToken(TokenIdentifier $token): Entity
    {
        $columns = $this->getColumns();
        $columns = array_merge($columns, [
            alias('oauth_access_tokens.id', 'oauth_access_tokens_id'),
            alias('oauth_access_tokens.identifier', 'oauth_access_tokens_identifier'),
            alias('oauth_access_tokens.expiration', 'oauth_access_tokens_expiration'),
            alias('oauth_access_tokens.revoked', 'oauth_access_tokens_revoked'),
            alias('oauth_access_tokens.created_at', 'oauth_access_tokens_created_at'),
            alias('oauth_access_tokens.updated_at', 'oauth_access_tokens_updated_at')
        ]);
        $query = $this->db->createQueryFactory()
            ->select(... $columns)
            ->from('users')
            ->join('oauth_access_tokens', on('users.id', 'oauth_access_tokens.user_id'))
            ->where(field('oauth_access_tokens.identifier')->eq(strval($token)))
            ->compile()
        ;

        $data = $this->db->execute($query)->fetch();
        if ($data) {
            $user = UserMapper::toDomain(
                new TableData($data, 'users_')
            );
            $token = AccessTokenMapper::toDomain(
                new TableData($data, 'oauth_access_tokens_')
            );
            return $user;
        }
        throw new NotFoundException('User');
    }
}
