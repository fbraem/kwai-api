<?php
/**
 * User Repository.
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Database\ColumnFilter;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Ability;
use Kwai\Modules\Users\Domain\Rule;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Infrastructure\Mappers\AbilityMapper;
use Kwai\Modules\Users\Infrastructure\Mappers\RuleMapper;
use Kwai\Modules\Users\Infrastructure\RulesTable;
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\AbilityRepository;
use Latitude\QueryBuilder\Query;
use Latitude\QueryBuilder\Query\SelectQuery;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * Class AbilityDatabaseRepository
 *
 * Ability repository for read/write Ability entity from/to a database.
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
final class AbilityDatabaseRepository implements AbilityRepository
{
    /**
     * The database connection
     */
    private Connection $db;

    /**
     * AbilityDatabaseRepository Constructor
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * @inheritDoc
     * @return Entity<Ability>
     */
    public function getById(int $id): Entity
    {
        $query = $this->createBaseQuery()
            ->where(field(Tables::ABILITIES . '.id')->eq($id))
            ->compile()
        ;

        try {
            $ability = $this->db->execute($query)->fetch();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        if ($ability) {
            $rules = $this->getRulesForAbilities([$id]);
            if (count($rules) > 0) {
                $ability->abilities_rules = $rules[$id];
            } else {
                $ability->abilities_rules = [];
            }

            $columnFilter = new ColumnFilter(Tables::ABILITIES . '_');
            return AbilityMapper::toDomain(
                $columnFilter->filter($ability)
            );
        }
        throw new NotFoundException('Ability');
    }

    /**
     * @inheritDoc
     * @param Entity<User> $user
     * @return Entity<Ability>[]
     */
    public function getByUser(Entity $user): array
    {
        $query = $this->createBaseQuery()
            ->join(
                Tables::USER_ABILITIES,
                on(Tables::USER_ABILITIES . '.ability_id', Tables::ABILITIES . '.id')
            )
            ->where(field(Tables::USER_ABILITIES . '.user_id')->eq($user->id()))
            ->compile()
        ;
        return $this->fetchAll($query);
    }

    /**
     * Fetch all abilities with their rules.
     * The id of the ability is used as key of the returned array.
     *
     * @param Query $query
     * @return Entity<Ability>[]
     * @throws RepositoryException
     */
    private function fetchAll(Query $query): array
    {
        try {
            $rows = $this->db->execute($query)->fetchAll();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        $columnFilter = new ColumnFilter(Tables::ABILITIES . '_');

        $abilities = [];
        foreach ($rows as $row) {
            $ability = AbilityMapper::toDomain(
                $columnFilter->filter($row)
            );
            $abilities[$ability->id()] = $ability;
        }

        $rules = $this->getRulesForAbilities(array_keys($abilities));
        foreach ($abilities as $ability) {
            if (isset($rules[$ability->id()])) {
                foreach ($rules[$ability->id()] as $rule) {
                    $ability->addRule($rule);
                }
            }
        }

        return $abilities;
    }

    /**
     * Get the the rules for all abilities. The returned array uses the id
     * of the ability as key. The value contains all rules of the ability.
     *
     * @param int[] $abilities Array with ids of abilities
     * @return Entity<Rule>[]
     * @throws RepositoryException
     */
    private function getRulesForAbilities(array $abilities): array
    {
        $aliasFn = fn($columnName) =>
        alias(
            join('.', [Tables::RULES, $columnName]),
            join('_', [Tables::RULES, $columnName])
        );

        $rulesTable = new RulesTable();
        $columns = array_merge(
            [
                $aliasFn('id'),
                $aliasFn('name'),
                $aliasFn('remark'),
                $aliasFn('created_at'),
                $aliasFn('updated_at')
            ],
            [
                alias(Tables::ABILITY_RULES . '.ability_id', 'ability_id'),
                alias(Tables::RULE_ACTIONS . '.name', Tables::RULES . '_action'),
                alias(Tables::RULE_SUBJECTS . '.name', Tables::RULES . '_subject')
            ]
        );
        $query = $this->db->createQueryFactory()
            ->select(... $columns)
            ->from(Tables::ABILITY_RULES)
            ->join(
                Tables::RULES,
                on(Tables::ABILITY_RULES . '.rule_id', Tables::RULES . '.id')
            )
            ->join(
                Tables::RULE_ACTIONS,
                on(Tables::RULES . '.action_id', Tables::RULE_ACTIONS . '.id')
            )
            ->join(
                Tables::RULE_SUBJECTS,
                on(Tables::RULES . '.subject_id', Tables::RULE_SUBJECTS . '.id')
            )
            ->where(field('ability_id')->in(... $abilities))
            ->compile()
        ;
        try {
            $stmt = $this->db->execute($query);
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $rule = RuleMapper::toDomain($rulesTable->filter($row));
            if (! isset($result[$row->ability_id])) {
                $result[$row->ability_id] = [];
            }
            $result[$row->ability_id][] = $rule;
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        $query = $this->createBaseQuery()->compile();
        return $this->fetchAll($query);
    }

    /**
     * @inheritDoc
     */
    public function create(Ability $ability): Entity
    {
        $data = AbilityMapper::toPersistence($ability);

        $query = $this->db->createQueryFactory()
            ->insert(Tables::ABILITIES)
            ->columns(
                ... array_keys($data)
            )
            ->values(
                ...array_values($data)
            )
            ->compile()
        ;

        try {
            $this->db->execute($query);
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        $entity = new Entity(
            $this->db->lastInsertId(),
            $ability
        );

        if (count($ability->getRules()) > 0) {
            $this->addRules($entity, $ability->getRules());
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function addRules(Entity $ability, array $rules)
    {
        if (count($rules) == 0) {
            return;
        }

        $query = $this->db->createQueryFactory()
            ->insert(Tables::ABILITY_RULES)
            ->columns(
                'ability_id',
                'rule_id'
            )
        ;
        foreach ($rules as $rule) {
            $query->values(
                $ability->id(),
                $rule->id()
            );
        }
        try {
            $this->db->execute($query->compile());
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteRules(Entity $ability, array $rules)
    {
        if (count($rules) == 0) {
            return;
        }

        $ruleIds = array_map(fn($rule) => $rule->id(), $rules);

        $query = $this->db->createQueryFactory()
            ->delete(Tables::ABILITY_RULES)
            ->where(field('ability_id')->eq($ability->id()))
            ->andWhere(field('rule_id')->in(...$ruleIds))
            ->compile()
        ;

        try {
            $this->db->execute($query);
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function update(Entity $ability): void
    {
        $query = $this->db->createQueryFactory()
            ->update(Tables::ABILITIES)
            ->set(AbilityMapper::toPersistence($ability->domain()))
            ->where(field('id')->eq($ability->id()))
            ->compile()
        ;
        try {
            $this->db->execute($query);
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    private function createBaseQuery(): SelectQuery
    {
        $aliasFn = fn($columnName) =>
            alias(
                join('.', [Tables::ABILITIES, $columnName]),
                join('_', [Tables::ABILITIES, $columnName])
            )
        ;
        return $this->db->createQueryFactory()
            ->select(...[
                $aliasFn('id'),
                $aliasFn('name'),
                $aliasFn('remark'),
                $aliasFn('created_at'),
                $aliasFn('updated_at')
            ])
            ->from(Tables::ABILITIES)
        ;
    }
}
