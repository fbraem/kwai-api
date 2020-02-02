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
use Kwai\Core\Infrastructure\Database;

use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Infrastructure\Mappers\AccessTokenMapper;
use Kwai\Modules\Users\Infrastructure\AccessTokenTable;

use Kwai\Modules\Users\Repositories\AccessTokenRepository;

use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\alias;

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
        $query = $this->db->createQueryFactory()
            ->select(... $this->table->alias())
            ->from($this->table->from())
            ->where(field('identifier')->eq(strval($identifier)))
            ->compile()
        ;

        $row = $this->db->execute($query)->fetch();
        if ($row) {
            return AccessTokenMapper::toDomain(
                $this->table->filter($row)
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
        $query = $this->db->createQueryFactory()
            ->select(... $this->table->alias())
            ->from($this->table->from())
            ->where(field('user_id')->eq($user->id()))
            ->compile()
        ;

        $rows = $this->db->execute($query)->fetchAll();

        $tokens = [];
        foreach ($rows as $row) {
            $token = AccessTokenMapper::toDomain(
                $this->table->filter($row)
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

        $query = $this->db->createQueryFactory()
            ->insert($this->table->from())
            ->columns(
                ... array_keys($data)
            )
            ->values(
                ... array_values($data)
            )
            ->compile()
        ;
        $stmt = $this->db->execute($query);

        return new Entity(
            $this->db->lastInsertId(),
            $token
        );
    }
}
