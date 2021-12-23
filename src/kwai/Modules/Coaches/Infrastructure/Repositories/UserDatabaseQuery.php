<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Infrastructure\Repositories;

use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Coaches\Infrastructure\Tables;
use Kwai\Modules\Coaches\Repositories\UserQuery;
use function Latitude\QueryBuilder\field;

/**
 * Class UserDatabaseQuery
 */
class UserDatabaseQuery extends DatabaseQuery implements UserQuery
{
    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query->from((string) Tables::USERS());
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $userAliasFn = Tables::USERS()->getAliasFn();

        return [
            $userAliasFn('id'),
            $userAliasFn('first_name'),
            $userAliasFn('last_name'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterId(int $id): UserQuery
    {
        $this->query->andWhere(
            field(Tables::USERS()->id)->eq($id)
        );
        return $this;
    }
}
