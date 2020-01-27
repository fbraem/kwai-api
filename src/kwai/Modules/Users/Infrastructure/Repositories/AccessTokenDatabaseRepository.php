<?php
/**
 * AccessToken Repository.
 * @package kwai
 * @subpackage Auth
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\Exceptions\NotCreatedException;
use Kwai\Core\Infrastructure\TableData;

use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Infrastructure\Mappers\AccessTokenMapper;
use Kwai\Modules\Users\Infrastructure\AccessTokenTable;

use Kwai\Modules\Users\Repositories\AccessTokenRepository;

use Opis\Database\Database;

/**
* AccessToken Repository for read/write AccessToken entity from/to a database.
*/
final class AccessTokenDatabaseRepository implements AccessTokenRepository
{
    /**
     * @var Database
     */
    private $db;

    /**
     * AccessToken table
     * @var AccessTokenTable
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
        $this->table = new AccessTokenTable();
    }

    /**
     * Get an accesstoken by its token identifier.
     *
     * @param  TokenIdentifier $identifier A token identifier
     * @return Entity<AccessToken>         An accesstoken
     */
    public function getByTokenIdentifier(TokenIdentifier $identifier) : Entity
    {
        $row = $this->db->from($this->table->from())
            ->where('identifier')->is(strval($identifier))
            ->select(function ($include) {
                $include->columns($this->table->alias());
            })
            ->first();
        if ($row) {
            return AccessTokenMapper::toDomain(
                new TableData($row, $this->table->prefix())
            );
        }
        throw new NotFoundException('AccessToken');
    }

    /**
     * Get all accesstokens of a user.
     * @param  Entity<User> $user
     * @return Entity<AccessToken>[]  An array with accesstokens
     */
    public function getTokensForUser(Entity $user): array
    {
        $rows = $this->db->from($this->table->from())
            ->where('user_id')->is($user->id())
            ->select(function ($include) {
                $include->columns($this->table->alias());
            })
            ->all();
        ;
        $tokens = [];
        foreach ($rows as $row) {
            $token = AccessTokenMapper::toDomain(
                new TableData($row, $this->table->prefix())
            );
            $token->attachUser($user);
            $tokens[] = $token;
        }
        return $tokens;
    }

    /**
     * Inserts the accesstoken in the table.
     * @param  AccessToken $token
     * @return Entity<User>
     * @throws NotCreatedException Thrown when insert fails
     */
    public function create(AccessToken $token): Entity
    {
        $data = AccessTokenMapper::toPersistence($token);

        $result = $this->db
            ->insert($data)
            ->into($this->table->from());
        if ($result) {
            return new Entity(
                (int) $this->db->getConnection()->getPDO()->lastInsertId(),
                $token
            );
        }
        throw new NotCreatedException('AccessToken');
    }
}
