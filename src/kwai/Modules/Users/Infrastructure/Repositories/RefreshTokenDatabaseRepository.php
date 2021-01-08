<?php
/**
 * AccessToken Repository.
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\RefreshTokenNotFoundException;
use Kwai\Modules\Users\Domain\RefreshToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Infrastructure\Mappers\RefreshTokenMapper;
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\RefreshTokenRepository;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * Class RefreshTokenDatabaseRepository
 *
 * RefreshToken Repository for read/write RefreshToken entity from/to a database.
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
final class RefreshTokenDatabaseRepository extends DatabaseRepository implements RefreshTokenRepository
{
    /**
     * @inheritDoc
     * @return Entity<RefreshToken>
     */
    public function getByTokenIdentifier(TokenIdentifier $identifier) : Entity
    {
        $aliasAccessTokenFn = Tables::ACCESS_TOKENS()->getAliasFn();
        $aliasRefreshTokenFn = Tables::REFRESH_TOKENS()->getAliasFn();
        $aliasUserFn = Tables::USERS()->getAliasFn();

        /** @noinspection PhpUndefinedFieldInspection */
        $query = $this->db->createQueryFactory()
            ->select(
                $aliasRefreshTokenFn('id'),
                $aliasRefreshTokenFn('identifier'),
                $aliasRefreshTokenFn('access_token_id'),
                $aliasRefreshTokenFn('expiration'),
                $aliasRefreshTokenFn('revoked'),
                $aliasRefreshTokenFn('created_at'),
                $aliasRefreshTokenFn('updated_at'),
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
            ->from((string) Tables::REFRESH_TOKENS())
            ->join(
                (string) Tables::ACCESS_TOKENS(),
                on(
                    Tables::REFRESH_TOKENS()->access_token_id,
                    Tables::ACCESS_TOKENS()->id
                )
            )
            ->join(
                (string) Tables::USERS(),
                on(
                    Tables::ACCESS_TOKENS()->user_id,
                    Tables::USERS()->id
                )
            )
            ->where(field(Tables::REFRESH_TOKENS()->identifier)->eq(strval($identifier)))
        ;

        try {
            $row = collect($this->db->execute($query)->fetch());
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        if ($row->isNotEmpty()) {
            [
                $refreshToken,
                $accessToken,
                $user
            ] = $row->filterColumns(new Collection([
                Tables::REFRESH_TOKENS()->getAliasPrefix(),
                Tables::ACCESS_TOKENS()->getAliasPrefix(),
                Tables::USERS()->getAliasPrefix()
            ]));
            $accessToken->put('user', $user);
            $refreshToken->put('accessToken', $accessToken);
            return new Entity(
                (int) $refreshToken->get('id'),
                RefreshTokenMapper::toDomain($refreshToken)
            );
        }
        throw new RefreshTokenNotFoundException($identifier);
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
