<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Rule;
use Kwai\Modules\Users\Infrastructure\Mappers\RuleMapper;
use Kwai\Modules\Users\Infrastructure\RulesTable;
use Kwai\Modules\Users\Infrastructure\RuleActionsTable;
use Kwai\Modules\Users\Infrastructure\RuleSubjectsTable;
use Kwai\Modules\Users\Repositories\RuleRepository;
use Latitude\QueryBuilder\Query;
use Latitude\QueryBuilder\Query\SelectQuery;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * RuleDatabaseRepository
 */
class RuleDatabaseRepository implements RuleRepository
{
    private Connection $db;

    private RulesTable $table;

    private RuleSubjectsTable $subjectsTable;

    private RuleActionsTable $actionsTable;

    /**
     * RuleDatabaseRepository constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
        $this->table = new RulesTable();
        $this->subjectsTable = new RuleSubjectsTable();
        $this->actionsTable = new RuleActionsTable();
    }

    /**
     * @inheritDoc
     */
    public function getAll(?string $subject = null): array
    {
        $select = $this->createBaseQuery();
        if ($subject) {
            $select->where(
                field($this->subjectsTable->column('name'))->eq($subject)
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
        $columns = array_merge(
            $this->table->alias(),
            [
                alias(
                    $this->actionsTable->column('name'),
                    // Trick the mapper with the 'rules_' prefix ...
                    $this->table->aliasColumn('action')
                ),
                alias(
                    $this->subjectsTable->column('name'),
                    // Trick the mapper with the 'rules_' prefix ...
                    $this->table->aliasColumn('subject')
                )
            ]
        );
        return $this->db->createQueryFactory()
            ->select(... $columns)
            ->from($this->table->from())
            ->join(
                $this->actionsTable->name(),
                on(
                    $this->table->column('action_id'),
                    $this->actionsTable->column('id')
                )
            )
            ->join(
                $this->subjectsTable->name(),
                on(
                    $this->table->column('subject_id'),
                    $this->subjectsTable->column('id')
                )
            )
        ;
    }

    /**
     * @inheritDoc
     */
    public function getByIds(array $ids): array
    {
        $query = $this->createBaseQuery()
            ->where(field($this->table->column('id'))->in(...$ids))
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

        $result = [];
        foreach ($rows as $row) {
            $rule = RuleMapper::toDomain($this->table->filter($row));
            $result[$rule->id()] = $rule;
        }

        return $result;
    }
}
