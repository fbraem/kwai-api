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
use Kwai\Modules\Users\Infrastructure\Mappers\RefreshTokenDTO;
use Kwai\Modules\Users\Infrastructure\RefreshTokenTable;
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
            fn(RefreshTokenDTO $item) => $item->createEntity()
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
        $dto = new RefreshTokenDTO();
        $data = $dto
            ->persist($token)
            ->refreshToken
            ->collect()
            ->forget('id')
        ;

        $query = $this->db->createQueryFactory()
            ->insert(RefreshTokenTable::name())
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
        $token->getTraceableTime()->markUpdated();
        $data = (new RefreshTokenDTO())
            ->persistEntity($token)
            ->refreshToken
            ->collect()
            ->forget('id')
        ;

        $query = $this->db->createQueryFactory()
            ->update(
                RefreshTokenTable::name(),
                $data->toArray()
            )
            ->where(field('id')->eq($token->id()))
        ;
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }
}
