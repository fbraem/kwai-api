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
use Kwai\Modules\Users\Infrastructure\RoleRulesTable;
use Kwai\Modules\Users\Infrastructure\Mappers\RoleDTO;
use Kwai\Modules\Users\Infrastructure\Mappers\RuleDTO;
use Kwai\Modules\Users\Infrastructure\RulesTable;
use Kwai\Modules\Users\Infrastructure\RuleSubjectsTable;
use Kwai\Modules\Users\Repositories\RoleQuery;
use function Latitude\QueryBuilder\on;

/**
 * Class RoleDatabaseQuery
 */
class RoleDatabaseQuery extends DatabaseQuery implements RoleQuery
{
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            RolesTable::column('id')
        );
    }

    /**
     * @inheritDoc
     */
    public function filterByIds(int... $ids): RoleQuery
    {
        $this->query->andWhere(
            RolesTable::field('id')->in(...$ids)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query
            ->from(RolesTable::name())
            ->leftJoin(
                RoleRulesTable::name(),
                on(
                    RolesTable::column('id'),
                    RoleRulesTable::column('role_id')
                )
            )
            ->leftJoin(
                RulesTable::name(),
                on(
                    RoleRulesTable::column('rule_id'),
                    RulesTable::column('id')
                )
            )
            ->leftJoin(
                RuleSubjectsTable::name(),
                on(
                    RulesTable::column('subject_id'),
                    RuleSubjectsTable::column('id')
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
            ...RolesTable::aliases(),
            ...RulesTable::aliases(),
            ...RuleSubjectsTable::aliases()
        ];
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

        $roles = new Collection();

        foreach ($rows as $row) {
            $role = RolesTable::createFromRow($row);
            $dto = $roles
                ->when(
                    !$roles->has($role->id),
                    fn ($collection) => $collection->put($role->id, new RoleDTO($role))
                )->get($role->id)
            ;
            $roleRules = RoleRulesTable::createFromRow($row);
            if ($roleRules->rule_id) {
                $dto->rules->push(
                    new RuleDTO(
                        RulesTable::createFromRow($row),
                        RuleSubjectsTable::createFromRow($row)
                    )
                );
            }
        }

        return $roles;
    }
}
