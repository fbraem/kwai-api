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

use Kwai\Modules\Users\Repositories\UserRepository;
use Kwai\Modules\Users\Infrastructure\Mappers\UserMapper;
use Kwai\Modules\Users\Domain\User;

use Opis\Database\Database;

/**
* User Repository for read/write User entity from/to a database.
*/
final class UserDatabaseRepository implements UserRepository
{
    /**
     * The name of the table
     * @var string
     */
    public const TABLE_NAME = 'users';

    /**
     * The prefix used in select statement
     * @var string
     */
    private const TABLE_NAME_PREFIX = self::TABLE_NAME . '_';

    /**
     * The column names of the table
     * @var string[]
     */
    public const COLUMNS = [
        'id',
        'email',
        'password',
        'last_login',
        'first_name',
        'last_name',
        'remark',
        'uuid',
        'created_at',
        'updated_at'
    ];

    /**
     * @var Database
     */
    private $db;

    /**
     * The columns with their prefix
     * @var string[]
     */
    private $columns;

    /**
     * Constructor
     *
     * @param Database $db A database object
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $aliases = array_map(function ($value) {
            return self::TABLE_NAME_PREFIX . $value;
        }, self::COLUMNS);
        $this->columns = array_combine(self::COLUMNS, $aliases);
    }

    /**
     * Get the user with the given id.
     * @param  int  $id The id of the user
     * @return User     The user
     * @throws NotFoundException When user is not found
     */
    public function getById(int $id): User
    {
        $user = $this->db->from(self::TABLE_NAME)
            ->where('id')->is($id)
            ->select(function ($include) {
                $include->columns($this->columns);
            })
            ->first();
        ;
        if ($user) {
            return UserMapper::toDomain(
                new TableData($user, self::TABLE_NAME_PREFIX)
            );
        }
        throw new NotFoundException('User');
    }

    public function getByUUID(UniqueId $uid): User
    {
    }
}
