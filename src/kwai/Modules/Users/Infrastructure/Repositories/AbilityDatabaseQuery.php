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
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\AbilityQuery;
use function Latitude\QueryBuilder\on;

/**
 * Class AbilityDatabaseQuery
 */
class AbilityDatabaseQuery extends DatabaseQuery implements AbilityQuery
{
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            Tables::ABILITIES->column('id')
        );
    }

    /**
     * @inheritDoc
     */
    public function filterById(int $id): AbilityQuery
    {
        $this->query->andWhere(
            Tables::ABILITIES->field('id')->eq($id)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query
            ->from(Tables::ABILITIES->value)
            ->leftJoin(
                Tables::ABILITY_RULES->value,
                on(
                    Tables::ABILITIES->column('id'),
                    Tables::ABILITY_RULES->column('ability_id')
                )
            )
            ->leftJoin(
                Tables::RULES->value,
                on(
                    Tables::ABILITY_RULES->column('rule_id'),
                    Tables::RULES->column('id')
                )
            )
            ->leftJoin(
                Tables::RULE_ACTIONS->value,
                on(
                    Tables::RULES->column('action_id'),
                    Tables::RULE_ACTIONS->column('id')
                )
            )
            ->leftJoin(
                Tables::RULE_SUBJECTS->value,
                on(
                    Tables::RULES->column('subject_id'),
                    Tables::RULE_SUBJECTS->column('id')
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
            ...Tables::ABILITIES->aliases('id', 'name', 'remark', 'created_at', 'updated_at'),
            ...Tables::RULES->aliases('name', 'remark', 'created_at', 'updated_at'),
            Tables::RULE_ACTIONS->alias('name', Tables::RULES->aliasPrefix() . 'action'),
            Tables::RULE_SUBJECTS->alias('name', Tables::RULES->aliasPrefix() . 'subject')
        ];
    }

    /**
     * @inheritDoc
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $abilities = new Collection();

        foreach ($rows as $row) {
            $ability = Tables::ABILITIES->collect($row);
            $rule = Tables::RULES->collect($row);
            if (!$abilities->has($ability->get('id'))) {
                $abilities->put($ability['id'], $ability);
                $ability->put('rules', new Collection());
            }
            if ($rule->has('id')) {
                $abilities
                    ->get($ability->get('id'))
                    ->get('rules')
                    ->push($rule);
            }
        }

        return $abilities;
    }
}
