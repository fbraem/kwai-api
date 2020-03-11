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
use Kwai\Modules\Users\Domain\Ability;
use Kwai\Modules\Users\Domain\Rule;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Infrastructure\AbilitiesTable;
use Kwai\Modules\Users\Infrastructure\Mappers\AbilityMapper;
use Kwai\Modules\Users\Infrastructure\Mappers\RuleMapper;
use Kwai\Modules\Users\Infrastructure\RulesTable;
use Kwai\Modules\Users\Repositories\AbilityRepository;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * Ability repository for read/write Ability entity from/to a database.
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
     * Constructor
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
        $this->table = new AbilitiesTable();
    }

    /**
     * Get the ability with the given id.
     * @param int $id
     * @return Entity<Ability>
     * @throws NotFoundException
     * @throws DatabaseException
     */
    public function getById(int $id): Entity
    {
        $query = $this->db->createQueryFactory()
            ->select(... $this->table->alias())
            ->from($this->table->from())
            ->where(field('abilities_id')->eq($id))
            ->compile()
        ;

        $ability = $this->db->execute($query)->fetch();
        if ($ability) {
            $rules = $this->getRulesForAbilities([$id]);
            $ability->abilities_rules = $rules[$id];
            $ability->ability_rules = [];

            return AbilityMapper::toDomain(
                $this->table->filter($ability)
            );
        }
        throw new NotFoundException('Ability');
    }

    /**
     * Get all abilities for the given user
     * @param Entity<User> $user
     * @return Entity<Ability>[]
     * @throws DatabaseException
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

        $rows = $this->db->execute($query)->fetchAll();

        $abilities = array_map(function ($row) {
            return AbilityMapper::toDomain(
                $this->table->filter($row)
            );
        }, $rows);

        $ability_ids = array_map(function (Entity $ability) {
            return $ability->id();
        }, $abilities);

        $rules = $this->getRulesForAbilities($ability_ids);
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
     * @param int[] $abilities Array with ids of abilities
     * @return Entity<Rule>[]
     * @throws DatabaseException
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
        $stmt = $this->db->execute($query);
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
}
