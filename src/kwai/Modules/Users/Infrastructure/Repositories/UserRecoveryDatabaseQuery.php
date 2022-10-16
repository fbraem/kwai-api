<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Users\Infrastructure\Mappers\UserRecoveryDTO;
use Kwai\Modules\Users\Infrastructure\UserRecoveriesTable;
use Kwai\Modules\Users\Infrastructure\UsersTable;
use Kwai\Modules\Users\Repositories\UserRecoveryQuery;
use function Latitude\QueryBuilder\on;

/**
 * Class UserRecoveryDatabaseQuery
 */
class UserRecoveryDatabaseQuery extends DatabaseQuery implements UserRecoveryQuery
{
    public function __construct(Connection $db)
    {
        parent::__construct($db);
    }

    public function filterById(int $id): UserRecoveryQuery
    {
        $this->query->andWhere(
            UserRecoveriesTable::field('id')->eq($id)
        );
        return $this;
    }

    public function filterByUUID(UniqueId $uuid): UserRecoveryQuery
    {
        $this->query->andWhere(
            UserRecoveriesTable::field('uuid')->eq((string) $uuid)
        );
        return $this;
    }

    protected function initQuery(): void
    {
        $this->query
            ->from(UserRecoveriesTable::name())
            ->join(
                UsersTable::name(),
                on(
                    UserRecoveriesTable::column('user_id'),
                    UsersTable::column('id')
                )
            )
        ;
    }

    protected function getColumns(): array
    {
        return [
            ... UserRecoveriesTable::aliases(),
            ... UsersTable::aliases()
        ];
    }

    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $userRecoveries = new Collection();

        foreach ($rows as $row) {
            $recovery = UserRecoveriesTable::createFromRow($row);
            $userRecoveries->put(
                $recovery->id,
                new UserRecoveryDTO(
                    $recovery,
                    UsersTable::createFromRow($row)
                )
            );
        }

        return $userRecoveries;
    }
}