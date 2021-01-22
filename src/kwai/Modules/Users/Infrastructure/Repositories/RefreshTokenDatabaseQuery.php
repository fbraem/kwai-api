<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\RefreshTokenQuery;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * Class RefreshTokenDatabaseQuery
 */
class RefreshTokenDatabaseQuery extends DatabaseQuery implements RefreshTokenQuery
{
    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query
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
        ;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $aliasAccessTokenFn = Tables::ACCESS_TOKENS()->getAliasFn();
        $aliasRefreshTokenFn = Tables::REFRESH_TOKENS()->getAliasFn();
        $aliasUserFn = Tables::USERS()->getAliasFn();

        return [
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
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterTokenIdentifier(TokenIdentifier $identifier): RefreshTokenQuery
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->where(
            field(Tables::REFRESH_TOKENS()->identifier)->eq(strval($identifier))
        )
        ;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $prefixes = [
            Tables::REFRESH_TOKENS()->getAliasPrefix(),
            Tables::ACCESS_TOKENS()->getAliasPrefix(),
            Tables::USERS()->getAliasPrefix()
        ];

        $refreshTokens = new Collection([]);
        foreach ($rows as $row) {
            [
                $refreshToken,
                $accessToken,
                $user
            ] = $row->filterColumns($prefixes);
            $accessToken->put('user', $user);
            $refreshToken->put('accessToken', $accessToken);
            $refreshTokens->put($refreshToken->get('id'), $refreshToken);
        }

        return $refreshTokens;
    }
}
