<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Infrastructure\Mappers\AccessTokenMapper;
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\AccessTokenRepository;
use function Latitude\QueryBuilder\field;

/**
 * Class AccessTokenDatabaseRepository
 *
 * AccessToken Repository for read/write AccessToken entity from/to a database.
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
final class AccessTokenDatabaseRepository extends DatabaseRepository implements AccessTokenRepository
{
    /**
     * @inheritDoc
     * @return Entity<AccessToken>
     */
    public function create(AccessToken $token): Entity
    {
        $data = AccessTokenMapper::toPersistence($token);

        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::ACCESS_TOKENS())
            ->columns(
                ... $data->keys()
            )
            ->values(
                ... $data->values()
            )
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
        /** @noinspection PhpUndefinedMethodInspection */
        $token->getTraceableTime()->markUpdated();

        $data = AccessTokenMapper::toPersistence($token->domain());
        $query = $this->db->createQueryFactory()
            ->update((string) Tables::ACCESS_TOKENS())
            ->set($data->toArray())
            ->where(field('id')->eq($token->id()))
        ;
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }
}
