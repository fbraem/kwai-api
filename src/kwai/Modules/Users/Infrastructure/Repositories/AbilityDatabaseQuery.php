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
use Kwai\Modules\Users\Infrastructure\AbilitiesTable;
use Kwai\Modules\Users\Infrastructure\AbilityRulesTable;
use Kwai\Modules\Users\Infrastructure\Mappers\AbilityDTO;
use Kwai\Modules\Users\Infrastructure\Mappers\RuleDTO;
use Kwai\Modules\Users\Infrastructure\RuleActionsTable;
use Kwai\Modules\Users\Infrastructure\RulesTable;
use Kwai\Modules\Users\Infrastructure\RuleSubjectsTable;
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
            AbilitiesTable::column('id')
        );
    }

    /**
     * @inheritDoc
     */
    public function filterById(int $id): AbilityQuery
    {
        $this->query->andWhere(
            AbilitiesTable::field('id')->eq($id)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query
            ->from(AbilitiesTable::getTableName())
            ->leftJoin(
                AbilityRulesTable::getTableName(),
                on(
                    AbilitiesTable::column('id'),
                    AbilityRulesTable::column('ability_id')
                )
            )
            ->leftJoin(
                RulesTable::getTableName(),
                on(
                    AbilityRulesTable::column('rule_id'),
                    RulesTable::column('id')
                )
            )
            ->leftJoin(
                RuleActionsTable::getTableName(),
                on(
                    RulesTable::column('action_id'),
                    RuleActionsTable::column('id')
                )
            )
            ->leftJoin(
                RuleSubjectsTable::getTableName(),
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
            ...AbilitiesTable::aliases(),
            ...RulesTable::aliases(),
            ...RuleActionsTable::aliases(),
            ...RuleSubjectsTable::aliases()
        ];
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return Collection<AbilityDTO>
     * @throws QueryException
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        /** @var Collection<AbilityDTO> $abilities */
        $abilities = new Collection();

        foreach ($rows as $row) {
            $ability = AbilitiesTable::createFromRow($row);
            $dto = $abilities
                ->when(
                    !$abilities->has($ability->id),
                    fn ($collection) => $collection->put($ability->id, new AbilityDTO($ability))
                )->get($ability->id)
            ;
            $abilityRules = AbilityRulesTable::createFromRow($row);
            if ($abilityRules->rule_id) {
                $dto->rules->push(
                    new RuleDTO(
                        RulesTable::createFromRow($row),
                        RuleActionsTable::createFromRow($row),
                        RuleSubjectsTable::createFromRow($row)
                    )
                );
            }
        }

        return $abilities;
    }
}
