<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Modules\Users\Infrastructure\RolesTable;
use Kwai\Modules\Users\Infrastructure\Mappers\RoleDTO;
use Kwai\Modules\Users\Infrastructure\Mappers\RuleDTO;
use Kwai\Modules\Users\Infrastructure\RuleActionsTable;
use Kwai\Modules\Users\Infrastructure\RulesTable;
use Kwai\Modules\Users\Infrastructure\RuleSubjectsTable;
use Kwai\Modules\Users\Infrastructure\UserRolesTable;
use Kwai\Modules\Users\Repositories\UserRoleQuery;
use function Latitude\QueryBuilder\on;

/**
 * Class UserRoleDatabaseQuery
 */
class UserRoleDatabaseQuery extends DatabaseQuery implements UserRoleQuery
{
    private RoleDatabaseQuery $roleQuery;

    public function __construct(Connection $db)
    {
        $this->roleQuery = new RoleDatabaseQuery($db);
        parent::__construct($db, UserRolesTable::column('user_id'));
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query = $this->roleQuery->query;
        $this->query->join(
            (string) UserRolesTable::name(),
            on(
                RolesTable::column('id'),
                UserRolesTable::column('role_id')
            )
        );
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        return [
            ...UserRolesTable::aliases(),
            ...$this->roleQuery->getColumns()
        ];
    }

    public function filterByUser(int ...$userIds): UserRoleQuery
    {
        $this->query->andWhere(
            UserRolesTable::field('user_id')->in(...$userIds)
        );
        return $this;
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return Collection<RoleDTO>
     * @throws QueryException
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $users = new Collection();

        foreach ($rows as $row) {
            $user_id = UserRolesTable::createFromRow($row)->user_id;
            $role = RolesTable::createFromRow($row);

            $userRoles = $users->when(
                !$users->has($user_id),
                fn ($collection) => $collection->put($user_id, new Collection())
            )->get($user_id);

            if (!$userRoles->has($role->id)) {
                $userRoles->put($role->id, new RoleDTO($role));
            }

            $ruleDTO = new RuleDTO(
                rule: RulesTable::createFromRow($row),
                action: RuleActionsTable::createFromRow($row),
                subject: RuleSubjectsTable::createFromRow($row)
            );
            $userRoles
                ->get($role->id)
                ->rules
                ->push($ruleDTO)
            ;
        }

        return $users;
    }
}
