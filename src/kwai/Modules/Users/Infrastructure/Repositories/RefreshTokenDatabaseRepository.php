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
use Kwai\Modules\Users\Domain\RefreshToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Infrastructure\AccessTokensTable;
use Kwai\Modules\Users\Infrastructure\Mappers\RefreshTokenMapper;
use Kwai\Modules\Users\Infrastructure\RefreshTokensTable;
use Kwai\Modules\Users\Infrastructure\UsersTable;
use Kwai\Modules\Users\Repositories\RefreshTokenRepository;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * RefreshToken Repository for read/write RefreshToken entity from/to a database.
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
final class RefreshTokenDatabaseRepository implements RefreshTokenRepository
{
    /**
     * The database connection
     */
    private Connection $db;

    /**
     * RefreshToken table
     */
    private RefreshTokensTable $table;

    /**
     * Constructor
     *
     * @param Connection $db A database object
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
        $this->table = new RefreshTokensTable();
    }

    /**
     * Get an refreshtoken by its token identifier.
     *
     * @param TokenIdentifier $identifier A token identifier
     * @return Entity<RefreshToken>         An refreshtoken
     * @throws DatabaseException
     * @throws NotFoundException
     */
    public function getByTokenIdentifier(TokenIdentifier $identifier) : Entity
    {
        $accessTokenTable = new AccessTokensTable();
        $userTable = new UsersTable();
        $columns = array_merge(
            $this->table->alias(),
            $accessTokenTable->alias(),
            $userTable->alias()
        );
        $query = $this->db->createQueryFactory()
            ->select(... $columns)
            ->from($this->table->from())
            ->join(
                $accessTokenTable->from(),
                on(
                    $this->table->from() . '.access_token_id',
                    $accessTokenTable->from() . '.id'
                )
            )
            ->join(
                $userTable->from(),
                on(
                    $accessTokenTable->from() . '.user_id',
                    $userTable->from() . '.id'
                )
            )
            ->where(field($this->table->from() . '.identifier')->eq(strval($identifier)))
            ->compile()
        ;

        $row = $this->db->execute($query)->fetch();

        if ($row) {
            $refreshTokenRow = $this->table->filter($row);
            $refreshTokenRow->accessToken = $accessTokenTable->filter($row);
            $refreshTokenRow->accessToken->user = $userTable->filter($row);
            return RefreshTokenMapper::toDomain($refreshTokenRow);
        }
        throw new NotFoundException('RefreshToken');
    }

    /**
     * Inserts the refreshtoken in the table.
     *
     * @param RefreshToken $token
     * @return Entity<RefreshToken>
     * @throws DatabaseException
     */
    public function create(RefreshToken $token): Entity
    {
        $data = RefreshTokenMapper::toPersistence($token);

        $query = $this->db->createQueryFactory()
            ->insert($this->table->from())
            ->columns(... array_keys($data))
            ->values(... array_values($data))
            ->compile()
        ;
        $this->db->execute($query);

        return new Entity(
            $this->db->lastInsertId(),
            $token
        );
    }

    /**
     * Update the refreshtoken.
     * @param Entity<RefreshToken> $token
     * @throws DatabaseException
     */
    public function update(Entity $token): void
    {
        $data = RefreshTokenMapper::toPersistence($token->domain());
        $query = $this->db->createQueryFactory()
            ->update($this->table->from(), $data)
            ->where(field('id')->eq($token->id()))
            ->compile()
        ;
        $this->db->execute($query);
    }
}
