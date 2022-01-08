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
use Kwai\Modules\Users\Infrastructure\RefreshTokenTable;
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Infrastructure\UsersTableSchema;
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
                UsersTableSchema::name(),
                on(
                    AccessTokenTable::column('user_id'),
                    UsersTableSchema::column('id')
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
            ...UsersTableSchema::aliases()
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

        /** @var Collection<RefreshTokenDTO> $refreshTokens */
        $refreshTokens = new Collection([]);
        foreach ($rows as $row) {
            $refreshToken = RefreshTokenTable::createFromRow($row);
            $refreshTokens->put(
                $refreshToken->id,
                new RefreshTokenDTO(
                    $refreshToken,
                    new AccessTokenDTO(
                        AccessTokenTable::createFromRow($row),
                        UsersTableSchema::createFromRow($row)
                    )
                )
            );
        }

        return $refreshTokens;
    }
}
