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
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Infrastructure\AccessTokensTable;
use Kwai\Modules\Users\Infrastructure\Mappers\AccessTokenMapper;
use Kwai\Modules\Users\Infrastructure\UsersTable;
use Kwai\Modules\Users\Repositories\AccessTokenRepository;
use Latitude\QueryBuilder\Query\SelectQuery;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * AccessToken Repository for read/write AccessToken entity from/to a database.
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
final class AccessTokenDatabaseRepository implements AccessTokenRepository
{
    /**
     * The database connection
     */
    private Connection $db;

    /**
     * AccessToken table
     */
    private AccessTokensTable $table;

    /**
     * User table
     */
    private UsersTable $userTable;

    /**
     * Constructor
     *
     * @param Connection $db A database object
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
        $this->table = new AccessTokensTable();
        $this->userTable = new UsersTable();
    }

    /**
     * Get an accesstoken by its token identifier.
     *
     * @param TokenIdentifier $identifier A token identifier
     * @return Entity<AccessToken>         An accesstoken
     * @throws DatabaseException
     * @throws NotFoundException
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
     *
     * @param Entity<User> $user
     * @return Entity<AccessToken>[]  An array with accesstokens
     * @throws DatabaseException
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
     *
     * @param AccessToken $token
     * @return Entity<AccessToken>
     * @throws DatabaseException
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
        $this->db->execute($query);

        return new Entity(
            $this->db->lastInsertId(),
            $token
        );
    }

    /**
     * @inheritdoc
     * @throws DatabaseException
     */
    public function update(Entity $token): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $token->getTraceableTime()->markUpdated();

        $data = AccessTokenMapper::toPersistence($token->domain());
        $query = $this->db->createQueryFactory()
            ->update($this->table->from(), $data)
            ->where(field('id')->eq($token->id()))
            ->compile()
        ;
        $this->db->execute($query);
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
