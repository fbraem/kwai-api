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
use Kwai\Core\Infrastructure\Database;

use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Infrastructure\Mappers\AccessTokenMapper;
use Kwai\Modules\Users\Infrastructure\AccessTokenTable;
use Kwai\Modules\Users\Infrastructure\UsersTable;

use Kwai\Modules\Users\Repositories\AccessTokenRepository;

use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\on;
use Latitude\QueryBuilder\Query\SelectQuery;

/**
* AccessToken Repository for read/write AccessToken entity from/to a database.
*/
final class AccessTokenDatabaseRepository implements AccessTokenRepository
{
    /**
     * @var Database\Connection
     */
    private $db;

    /**
     * AccessToken table
     * @var AccessTokenTable
     */
    private $table;

    /**
     * User table
     * @var UsersTable
     */
    private $userTable;

    /**
     * Constructor
     *
     * @param Database\Connection $db A database object
     */
    public function __construct(Database\Connection $db)
    {
        $this->db = $db;
        $this->table = new AccessTokenTable();
        $this->userTable = new UsersTable();
    }

    /**
     * Get an accesstoken by its token identifier.
     *
     * @param  TokenIdentifier $identifier A token identifier
     * @return Entity<AccessToken>         An accesstoken
     */
    public function getByTokenIdentifier(TokenIdentifier $identifier) : Entity
    {
        $query = $this->createBaseQuery()
            ->where(field('identifier')->eq(strval($identifier)))
            ->compile()
        ;

        $row = $this->db->execute($query)->fetch();
        if ($row) {
            $accessTokenRow = $this->table->filter($row);
            $accessTokenRow->user = $this->userTable->filter($row);
            return AccessTokenMapper::toDomain($accessTokenRow);
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
        $query = $this->createBaseQuery()
            ->where(field('user_id')->eq($user->id()))
            ->compile()
        ;

        $rows = $this->db->execute($query)->fetchAll();

        $tokens = [];
        foreach ($rows as $row) {
            $accessTokenRow = $this->table->filter($row);
            $accessTokenRow->user = $this->userTable->filter($row);
            $tokens[] = AccessTokenMapper::toDomain($accessTokenRow);
        }
        return $tokens;
    }

    /**
     * Inserts the accesstoken in the table.
     * @param  AccessToken $token
     * @return Entity<AccessToken>
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

    /**
     * @inheritdoc
     */
    public function update(Entity $token): void
    {
        $data = AccessTokenMapper::toPersistence($token->domain());
        $query = $this->db->createQueryFactory()
            ->update($this->table->from(), $data)
            ->where(field('id')->eq($token->id()))
            ->compile()
        ;
        $stmt = $this->db->execute($query);
    }

    /**
     * Create the base SELECT query
     * @return SelectQuery
     */
    private function createBaseQuery(): SelectQuery
    {
        $columns = array_merge(
            $this->table->alias(),
            $this->userTable->alias()
        );

        return $this->db->createQueryFactory()
            ->select(... $columns)
            ->from($this->table->from())
            ->join(
                $this->userTable->from(),
                on(
                    $this->table->from() . '.user_id',
                    $this->userTable->from() . '.id'
                )
            )
        ;
    }
}
