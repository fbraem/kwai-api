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
            ->from(Tables::REFRESH_TOKENS->value)
            ->join(
                Tables::ACCESS_TOKENS->value,
                on(
                    Tables::REFRESH_TOKENS->column('access_token_id'),
                    Tables::ACCESS_TOKENS->column('id')
                )
            )
            ->join(
                Tables::USERS->value,
                on(
                    Tables::ACCESS_TOKENS->column('user_id'),
                    Tables::USERS->column('id')
                )
            )
        ;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        return [
            ...Tables::REFRESH_TOKENS->aliases(
            'id',
                'identifier',
                'access_token_id',
                'expiration',
                'revoked',
                'created_at',
                'updated_at'
            ),
            ...Tables::ACCESS_TOKENS->aliases(
                'id',
                'identifier',
                'expiration',
                'revoked',
                'created_at',
                'updated_at'
            ),
            ...Tables::USERS->aliases(
                'id',
                'email',
                'password',
                'last_login',
                'first_name',
                'last_name',
                'remark',
                'uuid',
                'created_at',
                'updated_at'
            )
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterTokenIdentifier(TokenIdentifier $identifier): RefreshTokenQuery
    {
        $this->query->where(
            Tables::REFRESH_TOKENS->field('identifier')->eq(strval($identifier))
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

        $refreshTokens = new Collection([]);
        foreach ($rows as $row) {
            $accessToken = Tables::ACCESS_TOKENS->collect($row);
            $accessToken->put('user', Tables::USERS->collect($row));

            $refreshToken = Tables::REFRESH_TOKENS->collect($row);
            $refreshToken->put('accessToken', $accessToken);
            $refreshTokens->put($refreshToken->get('id'), $refreshToken);
        }

        return $refreshTokens;
    }
}
