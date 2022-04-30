<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\AccessTokenEntity;
use Kwai\Modules\Users\Infrastructure\AccessTokenTable;
use Kwai\Modules\Users\Infrastructure\Mappers\AccessTokenDTO;
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
            fn (AccessTokenDTO $item) => $item->createEntity()
        );
    }

    /**
     * @inheritDoc
     */
    public function create(AccessToken $token): AccessTokenEntity
    {
        $data = (new AccessTokenDTO())
            ->persist($token)
            ->accessToken
            ->collect()
            ->forget('id')
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

        return new AccessTokenEntity(
            $this->db->lastInsertId(),
            $token
        );
    }

    /**
     * @inheritdoc
     */
    public function update(AccessTokenEntity $token): void
    {
        $token->getTraceableTime()->markUpdated();

        $data = (new AccessTokenDTO())
            ->persistEntity($token)
            ->accessToken
            ->collect()
            ->forget('id')
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
