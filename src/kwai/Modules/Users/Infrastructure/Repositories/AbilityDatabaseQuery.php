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
use Kwai\Modules\Users\Infrastructure\AbilitiesTableSchema;
use Kwai\Modules\Users\Infrastructure\AbilityRulesTableSchema;
use Kwai\Modules\Users\Infrastructure\Mappers\AbilityDTO;
use Kwai\Modules\Users\Infrastructure\Mappers\RuleDTO;
use Kwai\Modules\Users\Infrastructure\RuleActionsTableSchema;
use Kwai\Modules\Users\Infrastructure\RulesTableSchema;
use Kwai\Modules\Users\Infrastructure\RuleSubjectsTableSchema;
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
            AbilitiesTableSchema::column('id')
        );
    }

    /**
     * @inheritDoc
     */
    public function filterById(int $id): AbilityQuery
    {
        $this->query->andWhere(
            AbilitiesTableSchema::field('id')->eq($id)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query
            ->from(AbilitiesTableSchema::getTableName())
            ->leftJoin(
                AbilityRulesTableSchema::getTableName(),
                on(
                    AbilitiesTableSchema::column('id'),
                    AbilityRulesTableSchema::column('ability_id')
                )
            )
            ->leftJoin(
                RulesTableSchema::getTableName(),
                on(
                    AbilityRulesTableSchema::column('rule_id'),
                    RulesTableSchema::column('id')
                )
            )
            ->leftJoin(
                RuleActionsTableSchema::getTableName(),
                on(
                    RulesTableSchema::column('action_id'),
                    RuleActionsTableSchema::column('id')
                )
            )
            ->leftJoin(
                RuleSubjectsTableSchema::getTableName(),
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
            ...AbilitiesTableSchema::aliases(),
            ...RulesTableSchema::aliases(),
            ...RuleActionsTableSchema::aliases(),
            ...RuleSubjectsTableSchema::aliases()
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
            $ability = AbilitiesTableSchema::createFromRow($row);
            $dto = $abilities
                ->when(
                    !$abilities->has($ability->id),
                    fn ($collection) => $collection->put($ability->id, new AbilityDTO($ability))
                )->get($ability->id)
            ;
            $abilityRules = AbilityRulesTableSchema::createFromRow($row);
            if ($abilityRules->rule_id) {
                $dto->rules->push(
                    new RuleDTO(
                        RulesTableSchema::createFromRow($row),
                        RuleActionsTableSchema::createFromRow($row),
                        RuleSubjectsTableSchema::createFromRow($row)
                    )
                );
            }
        }

        return $abilities;
    }
}
