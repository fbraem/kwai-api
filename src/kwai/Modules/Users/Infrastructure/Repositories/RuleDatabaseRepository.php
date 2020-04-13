<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Rule;
use Kwai\Modules\Users\Infrastructure\Mappers\RuleMapper;
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\RuleRepository;
use Latitude\QueryBuilder\Query;
use Latitude\QueryBuilder\Query\SelectQuery;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * RuleDatabaseRepository
 */
class RuleDatabaseRepository extends DatabaseRepository implements RuleRepository
{
    /**
     * @inheritDoc
     */
    public function getAll(?string $subject = null): array
    {
        $select = $this->createBaseQuery();
        if ($subject) {
            $select->where(
                field(Tables::RULE_SUBJECTS()->getColumn('name'))->eq($subject)
            );
        }
        $query = $select->compile();
        return $this->execute($query);
    }

    /**
     * Create the base query for SELECT
     *
     * @return SelectQuery
     */
    private function createBaseQuery(): SelectQuery
    {
        $aliasRulesFn = Tables::RULES()->getAliasFn();

        /** @noinspection PhpUndefinedFieldInspection */
        return $this->db->createQueryFactory()
            ->select(
                $aliasRulesFn('id'),
                $aliasRulesFn('name'),
                $aliasRulesFn('remark'),
                $aliasRulesFn('created_at'),
                $aliasRulesFn('updated_at'),
                // Trick the mapper with the 'rules_' prefix ...
                alias(
                    Tables::RULE_ACTIONS()->name,
                    Tables::RULES()->getAlias('action')
                ),
                // Trick the mapper with the 'rules_' prefix ...
                alias(
                    Tables::RULE_SUBJECTS()->name,
                    Tables::RULES()->getAlias('subject')
                )
            )
            ->from((string) Tables::RULES())
            ->join(
                (string) Tables::RULE_ACTIONS(),
                on(
                    Tables::RULES()->action_id,
                    Tables::RULE_ACTIONS()->id
                )
            )
            ->join(
                (string) Tables::RULE_SUBJECTS(),
                on(
                    Tables::RULES()->subject_id,
                    Tables::RULE_SUBJECTS()->id
                )
            )
        ;
    }

    /**
     * @inheritDoc
     */
    public function getByIds(array $ids): array
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $query = $this->createBaseQuery()
            ->where(field(Tables::RULES()->id)->in(...$ids))
            ->compile()
        ;
        return $this->execute($query);
    }

    /**
     * Execute the query and return an array of Rule entities.
     *
     * @param Query $query
     * @return Entity<Rule>[]
     * @throws RepositoryException
     */
    private function execute(Query $query): array
    {
        try {
            $stmt = $this->db->execute($query);
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        $rows = $stmt->fetchAll();

        $columnFilter = Tables::RULES()->createColumnFilter();
        $result = [];
        foreach ($rows as $row) {
            $rule = RuleMapper::toDomain($columnFilter->filter($row));
            $result[$rule->id()] = $rule;
        }

        return $result;
    }
}
