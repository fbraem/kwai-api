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
use Kwai\Modules\Users\Infrastructure\RuleActionsTable;
use Kwai\Modules\Users\Infrastructure\RulesTable;
use Kwai\Modules\Users\Infrastructure\RuleSubjectsTable;
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
            ->from(RulesTable::name())
            ->join(
                RuleActionsTable::name(),
                on(
                    RulesTable::column('action_id'),
                    RuleActionsTable::column('id')
                )
            )
            ->join(
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
            ...RulesTable::aliases(),
            ...RuleActionsTable::aliases(),
            ...RuleSubjectsTable::aliases()
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterById(int ...$id): RuleQuery
    {
        $this->query->andWhere(
            RulesTable::field('id')->in(...$id)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterBySubject(string $subject): RuleQuery
    {
        $this->query->andWhere(
            RuleSubjectsTable::field('name')->eq($subject)
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
            $rule = RulesTable::createFromRow($row);
            $rules->put(
                $rule->id,
                new RuleDTO(
                    $rule,
                    RuleActionsTable::createFromRow($row),
                    RuleSubjectsTable::createFromRow($row)
                )
            );
        }

        return $rules;
    }
}
