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
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Ability;
use Kwai\Modules\Users\Domain\Rule;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Infrastructure\AbilitiesTable;
use Kwai\Modules\Users\Infrastructure\AbilityRulesTable;
use Kwai\Modules\Users\Infrastructure\Mappers\AbilityMapper;
use Kwai\Modules\Users\Infrastructure\Mappers\RuleMapper;
use Kwai\Modules\Users\Infrastructure\RulesTable;
use Kwai\Modules\Users\Repositories\AbilityRepository;
use Latitude\QueryBuilder\Query;
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
     * Main table for this repository.
     */
    private AbilitiesTable $table;

    /**
     * AbilityDatabaseRepository Constructor
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
        $this->table = new AbilitiesTable();
    }

    /**
     * @inheritDoc
     * @return Entity<Ability>
     */
    public function getById(int $id): Entity
    {
        $query = $this->db->createQueryFactory()
            ->select(... $this->table->alias())
            ->from($this->table->from())
            ->where(field('id')->eq($id))
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
            // $ability->ability_rules = [];

            return AbilityMapper::toDomain(
                $this->table->filter($ability)
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
        $query = $this->db->createQueryFactory()
            ->select(... $this->table->alias())
            ->from('user_abilities')
            ->join(
                $this->table->from(),
                on('user_abilities.ability_id', 'abilities.id')
            )
            ->where(field('user_id')->eq($user->id()))
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

        $abilities = [];
        foreach ($rows as $row) {
            $ability = AbilityMapper::toDomain(
                $this->table->filter($row)
            );
            $abilities[$ability->id()] = $ability;
        }

        $rules = $this->getRulesForAbilities(array_keys($abilities));
        foreach ($abilities as $ability) {
            foreach ($rules[$ability->id()] as $rule) {
                $ability->addRule($rule);
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
        $rulesTable = new RulesTable();
        $columns = array_merge(
            $rulesTable->alias(),
            [
                alias('ability_rules.ability_id', 'ability_id'),
                alias('rule_actions.name', 'rules_action'),
                alias('rule_subjects.name', 'rules_subject')
            ]
        );
        $query = $this->db->createQueryFactory()
            ->select(... $columns)
            ->from('ability_rules')
            ->join('rules', on('ability_rules.rule_id', 'rules.id'))
            ->join('rule_actions', on('rules.action_id', 'rule_actions.id'))
            ->join('rule_subjects', on('rules.subject_id', 'rule_subjects.id'))
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
        $query = $this->db->createQueryFactory()
            ->select(... $this->table->alias())
            ->from($this->table->from())
            ->compile()
        ;

        return $this->fetchAll($query);
    }

    /**
     * @inheritDoc
     */
    public function create(Ability $ability): Entity
    {
        $data = AbilityMapper::toPersistence($ability);

        $query = $this->db->createQueryFactory()
            ->insert($this->table->from())
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
            $abilityRulesTable = new AbilityRulesTable();
            $query = $this->db->createQueryFactory()
                ->insert($abilityRulesTable->from())
                ->columns(
                    'ability_id',
                    'rule_id'
                );
            foreach ($ability->getRules() as $rule) {
                $query->values(
                    $entity->id(),
                    $rule->id()
                );
            }
            try {
                $this->db->execute($query->compile());
            } catch (DatabaseException $e) {
                throw new RepositoryException(__METHOD__, $e);
            }
        }

        return $entity;
    }
}
