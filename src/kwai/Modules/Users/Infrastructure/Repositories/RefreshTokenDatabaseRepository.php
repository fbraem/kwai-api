<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\RefreshTokenNotFoundException;
use Kwai\Modules\Users\Domain\RefreshToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Infrastructure\Mappers\RefreshTokenMapper;
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\RefreshTokenQuery;
use Kwai\Modules\Users\Repositories\RefreshTokenRepository;
use function Latitude\QueryBuilder\field;

/**
 * Class RefreshTokenDatabaseRepository
 *
 * RefreshToken Repository for read/write RefreshToken entity from/to a database.
 */
final class RefreshTokenDatabaseRepository extends DatabaseRepository implements RefreshTokenRepository
{
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            fn($item) => RefreshTokenMapper::toDomain($item)
        );
    }

    /**
     * Factory method
     *
     * @return RefreshTokenQuery
     */
    public function createQuery(): RefreshTokenQuery
    {
        return new RefreshTokenDatabaseQuery($this->db);
    }

    /**
     * @inheritDoc
     * @return Entity<RefreshToken>
     */
    public function getByTokenIdentifier(TokenIdentifier $identifier) : Entity
    {
        $query = $this->createQuery()->filterTokenIdentifier($identifier);
        $tokens = $this->getAll($query);
        if ($tokens->isEmpty()) {
            throw new RefreshTokenNotFoundException($identifier);
        }
        return $tokens->first();
    }

    /**
     * @inheritDoc
     * @return Entity<RefreshToken>
     */
    public function create(RefreshToken $token): Entity
    {
        $data = RefreshTokenMapper::toPersistence($token);

        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::REFRESH_TOKENS())
            ->columns(... $data->keys())
            ->values(... $data->values())
        ;
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        return new Entity(
            $this->db->lastInsertId(),
            $token
        );
    }

    /**
     * @inheritDoc
     */
    public function update(Entity $token): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $token->getTraceableTime()->markUpdated();

        $data = RefreshTokenMapper::toPersistence($token->domain());
        $query = $this->db->createQueryFactory()
            ->update((string) Tables::REFRESH_TOKENS(), $data->toArray())
            ->where(field('id')->eq($token->id()))
        ;
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }
}
