<?php
/**
 * User Repository.
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\DateTime;

use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\Username;
use Kwai\Modules\Users\Domain\Password;
use Kwai\Modules\Users\Repositories\UserRepository;

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

    /**
     * Constructor
     *
     * @param Database $db A database object
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getById(int $id): User
    {
        [
            'uuid' => $uuid,
            'email' => $email,
            'password' => $password,
            'first_name' => $firstname,
            'last_name' => $lastname,
            'remark' => $remark,
            'last_login' => $lastLogin,
            'remark' => $remark,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ] = $this->db->from('users')
            ->where('id')->is($id)
            ->select()
            ->first();
        ;

        return new User(
            new UniqueId($uuid),
            new EmailAddress($email),
            new TraceableTime(
                DateTime::createFromString($createdAt),
                isset($updatedAt) ? DateTime::createFromString($updatedAt) : null
            ),
            isset($lastLogin) ? DateTime::createFromString($lastLogin) : null,
            $remark,
            new Username($firstname, $lastname),
            new Password($password)
        );
    }

    public function getByUUID(UniqueId $uid): User
    {
    }
}
