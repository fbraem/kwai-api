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
use Kwai\Core\Infrastructure\Repositories\Query;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Infrastructure\AccessTokenTable;
use Kwai\Modules\Users\Infrastructure\Mappers\AccessTokenDTO;
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\AccessTokenQuery;
use Kwai\Modules\Users\Repositories\AccessTokenRepository;
use function Latitude\QueryBuilder\field;

/**
 * Class AccessTokenDatabaseRepository
 *
 * AccessToken Repository for read/write AccessToken entity from/to a database.
 */
final class AccessTokenDatabaseRepository extends DatabaseRepository implements AccessTokenRepository
{
    /**
     * AccessTokenDatabaseRepository constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            fn ($item) => (new AccessTokenDTO($item))->toDomain()
        );
    }

    /**
     * @inheritDoc
     * @return Entity<AccessToken>
     */
    public function create(AccessToken $token): Entity
    {
        $data = (new AccessTokenDTO())
            ->persist($token)
            ->accessToken
            ->collect()
        ;

        $query = $this->db->createQueryFactory()
            ->insert(AccessTokenTable::name())
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
     * @inheritdoc
     */
    public function update(Entity $token): void
    {
        $token->getTraceableTime()->markUpdated();

        $data = (new AccessTokenDTO())
            ->persistEntity($token)
            ->accessToken
            ->collect()
        ;

        $query = $this->db->createQueryFactory()
            ->update(AccessTokenTable::name())
            ->set($data->toArray())
            ->where(field('id')->eq($token->id()))
        ;
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    public function createQuery(): AccessTokenQuery
    {
        return new AccessTokenDatabaseQuery($this->db);
    }
}
