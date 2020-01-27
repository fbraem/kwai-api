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

use Kwai\Modules\Users\Repositories\UserRepository;
use Kwai\Modules\Users\Infrastructure\Mappers\UserMapper;
use Kwai\Modules\Users\Infrastructure\Mappers\AccessTokenMapper;
use Kwai\Modules\Users\Infrastructure\UserTable;
use Kwai\Modules\Users\Infrastructure\AccessTokenTable;

use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Core\Domain\EmailAddress;

use Opis\Database\Database;

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

    /**
     * Get the user with the given id.
     * @param  int  $id         The id of the user
     * @return Entity<User>     The user
     * @throws NotFoundException When user is not found
     */
    public function getById(int $id): Entity
    {
        $user = $this->db->from($this->table->from())
            ->where('id')->is($id)
            ->select(function ($include) {
                $include->columns($this->table->alias());
            })
            ->first()
        ;
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
        $user = $this->db->from($this->table->from())
            ->where('uuid')->is(strval($uid))
            ->select(function ($include) {
                $include->columns($this->table->alias());
            })
            ->first()
        ;
        if ($user) {
            return UserMapper::toDomain(
                new TableData($user, $this->table->prefix())
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
        $user = $this->db->from($this->table->from())
            ->where('email')->is(strval($email))
            ->select(function ($include) {
                $include->columns($this->table->alias());
            })
            ->first()
        ;
        if ($user) {
            return UserMapper::toDomain(
                new TableData($user, $this->table->prefix())
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
        $accessTokenTable = new AliasTable(new AccessTokenTable(), 'oat');
        $data = $this->db->from($this->table->from())
            ->where('oat.identifier')->is(strval($token))
            ->leftJoin(
                $accessTokenTable->from(),
                function ($join) {
                    $join->on('users.id', 'oat.user_id');
                }
            )
            ->select(function ($include) use ($accessTokenTable) {
                $include->columns($this->table->alias());
                $include->columns($accessTokenTable->alias());
            })
            ->first()
        ;
        if ($data) {
            $user = UserMapper::toDomain(
                new TableData($data, $this->table->prefix())
            );
            $token = AccessTokenMapper::toDomain(
                new TableData($data, $accessTokenTable->prefix())
            );
            return $user;
        }
        throw new NotFoundException('User');
    }
}
