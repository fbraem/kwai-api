<?php
/**
 * AccessToken Repository.
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Infrastructure\Mappers\AccessTokenMapper;
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\AccessTokenRepository;
use Latitude\QueryBuilder\Query\SelectQuery;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

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
    public function getByTokenIdentifier(TokenIdentifier $identifier) : Entity
    {
        $query = $this->createBaseQuery()
            ->where(field('identifier')->eq(strval($identifier)))
        ;

        try {
            $row = $this->db->execute($query)->fetch();
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        if ($row) {
            $accessTokenRow = Tables::ACCESS_TOKENS()
                ->createColumnFilter()
                ->filter($row)
            ;
            $accessTokenRow->user = Tables::USERS()
                ->createColumnFilter()
                ->filter($row)
            ;
            return AccessTokenMapper::toDomain($accessTokenRow);
        }
        throw new NotFoundException('AccessToken');
    }

    /**
     * @inheritDoc
     * @param Entity<User> $user
     * @return Entity<AccessToken>[]
     */
    public function getTokensForUser(Entity $user): array
    {
        $query = $this->createBaseQuery()
            ->where(field('user_id')->eq($user->id()))
        ;

        try {
            $rows = $this->db->execute($query)->fetchAll();
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        $accessTokenColumnFilter = Tables::ACCESS_TOKENS()->createColumnFilter();
        $userColumnFilter = Tables::USERS()->createColumnFilter();

        $tokens = [];
        foreach ($rows as $row) {
            $accessTokenRow = $accessTokenColumnFilter->filter($row);
            $accessTokenRow->user = $userColumnFilter->filter($row);
            $tokens[] = AccessTokenMapper::toDomain($accessTokenRow);
        }
        return $tokens;
    }

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
                ... array_keys($data)
            )
            ->values(
                ... array_values($data)
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
            ->update((string) Tables::ACCESS_TOKENS(), $data)
            ->where(field('id')->eq($token->id()))
        ;
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    /**
     * Create the base SELECT query
     * @return SelectQuery
     */
    private function createBaseQuery(): SelectQuery
    {
        $aliasAccessTokenFn = Tables::ACCESS_TOKENS()->getAliasFn();
        $aliasUserFn = Tables::USERS()->getAliasFn();

        /** @noinspection PhpUndefinedFieldInspection */
        return $this->db->createQueryFactory()
            ->select(
                $aliasAccessTokenFn('id'),
                $aliasAccessTokenFn('identifier'),
                $aliasAccessTokenFn('expiration'),
                $aliasAccessTokenFn('revoked'),
                $aliasAccessTokenFn('created_at'),
                $aliasAccessTokenFn('updated_at'),
                $aliasUserFn('id'),
                $aliasUserFn('email'),
                $aliasUserFn('password'),
                $aliasUserFn('last_login'),
                $aliasUserFn('first_name'),
                $aliasUserFn('last_name'),
                $aliasUserFn('remark'),
                $aliasUserFn('uuid'),
                $aliasUserFn('created_at'),
                $aliasUserFn('updated_at')
            )
            ->from((string) Tables::ACCESS_TOKENS())
            ->join(
                (string) Tables::USERS(),
                on(Tables::ACCESS_TOKENS()->user_id, Tables::USERS()->id)
            )
        ;
    }
}
