<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Users\Infrastructure\AccessTokenTable;
use Kwai\Modules\Users\Repositories\AccessTokenQuery;

/**
 * Class AccessTokenDatabaseQuery
 */
class AccessTokenDatabaseQuery extends DatabaseQuery implements AccessTokenQuery
{
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            AccessTokenTable::column('id')
        );
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query->from(AccessTokenTable::name());
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        return AccessTokenTable::aliases();
    }
}
