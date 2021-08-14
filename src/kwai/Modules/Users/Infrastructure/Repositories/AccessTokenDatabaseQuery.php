<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\AccessTokenQuery;

/**
 * Class AccessTokenDatabaseQuery
 */
class AccessTokenDatabaseQuery extends DatabaseQuery implements AccessTokenQuery
{
    public function __construct(Connection $db)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        parent::__construct($db, Tables::ACCESS_TOKENS()->id);
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query->from((string) Tables::ACCESS_TOKENS());
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $aliasFn = Tables::ACCESS_TOKENS()->getAliasFn();

        return [
            $aliasFn('id')
        ];
    }
}
