<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Infrastructure\AccessTokenTable;
use Kwai\Modules\Users\Infrastructure\Mappers\AccessTokenDTO;
use Kwai\Modules\Users\Infrastructure\Mappers\RefreshTokenDTO;
use Kwai\Modules\Users\Infrastructure\Mappers\UserAccountDTO;
use Kwai\Modules\Users\Infrastructure\RefreshTokenTable;
use Kwai\Modules\Users\Infrastructure\UsersTable;
use Kwai\Modules\Users\Repositories\RefreshTokenQuery;
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
        $this->query
            ->from(RefreshTokenTable::name())
            ->join(
                AccessTokenTable::name(),
                on(
                    RefreshTokenTable::column('access_token_id'),
                    AccessTokenTable::column('id')
                )
            )
            ->join(
                UsersTable::name(),
                on(
                    AccessTokenTable::column('user_id'),
                    UsersTable::column('id')
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
            ...RefreshTokenTable::aliases(),
            ...AccessTokenTable::aliases(),
            ...UsersTable::aliases()
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterTokenIdentifier(TokenIdentifier $identifier): RefreshTokenQuery
    {
        $this->query->where(
            RefreshTokenTable::field('identifier')->eq(strval($identifier))
        )
        ;
        return $this;
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return Collection<RefreshTokenDTO>
     * @throws QueryException
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $refreshTokens = new Collection([]);
        foreach ($rows as $row) {
            $refreshToken = RefreshTokenTable::createFromRow($row);
            $refreshTokens->put(
                $refreshToken->id,
                new RefreshTokenDTO(
                    $refreshToken,
                    new AccessTokenDTO(
                        AccessTokenTable::createFromRow($row),
                        new UserAccountDTO(UsersTable::createFromRow($row))
                    )
                )
            );
        }

        return $refreshTokens;
    }
}
