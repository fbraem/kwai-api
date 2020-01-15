<?php
/**
 * User Repository.
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\UniqueId;
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
     * @param  int  $id The id of the user
     * @return User     The user
     * @throws NotFoundException When user is not found
     */
    public function getById(int $id): User
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

    public function getByUUID(UniqueId $uid): User
    {
    }

    public function getByAccessToken(TokenIdentifier $token): User
    {
        $accessTokenTable = new AliasTable(new AccessTokenTable(), 'oat');
        $data = $this->db->from($this->table->from())
            ->where('oat.identifier')->is(strval($token))
            ->leftJoin(
                $accessTokenTable->from(),
                function ($join) use ($token) {
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
            echo var_dump($token), PHP_EOL;
            return $user;
        }
        throw new NotFoundException('User');
    }
}
