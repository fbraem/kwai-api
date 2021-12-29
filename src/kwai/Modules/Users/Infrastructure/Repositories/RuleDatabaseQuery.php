<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\RuleQuery;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * Class RuleDatabaseQuery
 */
class RuleDatabaseQuery extends DatabaseQuery implements RuleQuery
{
    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query
            ->from(Tables::RULES->value)
            ->join(
                Tables::RULE_ACTIONS->value,
                on(
                    Tables::RULES->column('action_id'),
                    Tables::RULE_ACTIONS->column('id')
                )
            )
            ->join(
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
            ...Tables::RULES->aliases('id', 'name', 'remark', 'created_at', 'updated_at'),
            // Trick the mapper with the 'rules_' prefix ...
            Tables::RULE_ACTIONS->alias('name', Tables::RULES->aliasPrefix() . 'action'),
            // Trick the mapper with the 'rules_' prefix ...
            Tables::RULE_SUBJECTS->alias('name', Tables::RULES->aliasPrefix() . 'subject')
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterById(int ...$id): RuleQuery
    {
        $this->query->andWhere(
            Tables::RULES->field('id')->in(...$id)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterBySubject(string $subject): RuleQuery
    {
        $this->query->andWhere(
            Tables::RULE_SUBJECTS->field('name')->eq($subject)
        );
        return $this;
    }

    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $rules = new Collection();

        foreach ($rows as $row) {
            $rule = Tables::RULES->collect($row);
            $rules->put($rule->get('id'), $rule);
        }

        return $rules;
    }
}
