<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Modules\Users\Infrastructure\Mappers\RuleDTO;
use Kwai\Modules\Users\Infrastructure\RuleActionsTableSchema;
use Kwai\Modules\Users\Infrastructure\RulesTableSchema;
use Kwai\Modules\Users\Infrastructure\RuleSubjectsTableSchema;
use Kwai\Modules\Users\Repositories\RuleQuery;
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
            ->from(RulesTableSchema::name())
            ->join(
                RuleActionsTableSchema::name(),
                on(
                    RulesTableSchema::column('action_id'),
                    RuleActionsTableSchema::column('id')
                )
            )
            ->join(
                RuleSubjectsTableSchema::name(),
                on(
                    RulesTableSchema::column('subject_id'),
                    RuleSubjectsTableSchema::column('id')
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
            ...RulesTableSchema::aliases(),
            ...RuleActionsTableSchema::aliases(),
            ...RuleSubjectsTableSchema::aliases()
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterById(int ...$id): RuleQuery
    {
        $this->query->andWhere(
            RulesTableSchema::field('id')->in(...$id)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterBySubject(string $subject): RuleQuery
    {
        $this->query->andWhere(
            RuleSubjectsTableSchema::field('name')->eq($subject)
        );
        return $this;
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return Collection<RuleDTO>
     * @throws QueryException
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        /** @var Collection<RuleDTO> $rules */
        $rules = new Collection();

        foreach ($rows as $row) {
            $rule = RulesTableSchema::createFromRow($row);
            $rules->put(
                $rule->id,
                new RuleDTO(
                    $rule,
                    RuleActionsTableSchema::createFromRow($row),
                    RuleSubjectsTableSchema::createFromRow($row)
                )
            );
        }

        return $rules;
    }
}
