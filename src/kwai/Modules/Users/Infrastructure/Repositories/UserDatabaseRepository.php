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

    /**
     * The table for 'users'
     * @var UserTable
     */
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

    /**
     * Get the user with the given id.
     * @param  int  $id         The id of the user
     * @return Entity<User>     The user
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
     * @param  UniqueId $uid
     * @return Entity<User>
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
     * Get the user with the given email.
     * @param  EmailAddress  $email The email address of the user
     * @return Entity<User>         The user
     * @throws NotFoundException    When user is not found
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
     * Get a user using a token
     * @param  TokenIdentifier $token
     * @return Entity<User>
     * @throws NotFoundException    When user is not found
     */
    public function getByAccessToken(TokenIdentifier $token): Entity
    {
        $accessTokenTable = new AccessTokenTable();
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

            $user = UserMapper::toDomain($userRow);
            // TODO: return token also?
            // $token = AccessTokenMapper::toDomain($accessTokenRow);
            return $user;
        }
        throw new NotFoundException('User');
    }
}
