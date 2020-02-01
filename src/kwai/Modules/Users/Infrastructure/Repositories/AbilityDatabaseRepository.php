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

use Kwai\Core\Infrastructure\TableData;
use Kwai\Core\Infrastructure\AliasTable;
use Kwai\Core\Infrastructure\DefaultTable;
use Kwai\Core\Infrastructure\Database;

use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\Ability;
use Kwai\Modules\Users\Domain\Rule;
use Kwai\Modules\Users\Repositories\AbilityRepository;
use Kwai\Modules\Users\Infrastructure\Mappers\AbilityMapper;
use Kwai\Modules\Users\Infrastructure\Mappers\RuleMapper;
use Kwai\Modules\Users\Infrastructure\AbilityTable;

use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\on;

/**
* Ability repository for read/write Ability entity from/to a database.
*/
final class AbilityDatabaseRepository implements AbilityRepository
{
    /**
     * @var Database
     */
    private $db;

    /**
     * Main table for this repository.
     * @var AbilityTable
     */
    private $table;

    /**
     * Constructor
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->table = new AbilityTable();
    }

    private function getColumns()
    {
        return [
            alias('abilities.id', 'abilities_id'),
            alias('abilities.name', 'abilities_name'),
            alias('abilities.remark', 'abilities_remark'),
            alias('abilities.created_at', 'abilities_created_at'),
            alias('abilities.updated_at', 'abilities_updated_at')
        ];
    }

    /**
     * Get the ability with the given id.
     * @param  int    $id
     * @return Entity<Ability>
     */
    public function getById(int $id): Entity
    {
        $query = $this->db->createQueryFactory()
            ->select(... $this->getColumns())
            ->from('abilities')
            ->where(field('abilities_id')->eq($id))
            ->compile()
        ;
        $stmt = $this->db->execute($query);
        $ability = $stmt->fetch();

        if ($ability) {
            $rules = $this->getRulesForAbilities([$id]);
            $ability->abilities_rules = $rules[$id];
            $ability->ability_rules = [];

            return AbilityMapper::toDomain(
                new TableData($ability, 'abilities_')
            );
        }
        throw new NotFoundException('Ability');
    }

    /**
     * Get all abilities for the given user
     * @param  Entity<User> $user
     * @return Entity<Ability>[]
     */
    public function getByUser(Entity $user): array
    {
        $rows = $this->db->from('user_abilities')
            ->where('user_id')->is($user->id())
            ->leftJoin(
                $this->table->name(),
                function ($join) {
                    $join->on('user_abilities.ability_id', 'abilities.id');
                }
            )
            ->select(function ($include) {
                $include->columns($this->table->alias());
            })
            ->all()
        ;
        $abilities = array_map(function ($row) {
            return AbilityMapper::toDomain(
                new TableData($row, $this->table->prefix())
            );
        }, $rows);
        $ability_ids = array_map(function ($ability) {
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
     * @param  int[] $abilities Array with ids of abilities
     * @return Entity<Rule>[]
     */
    private function getRulesForAbilities(array $abilities): array
    {
        $query = $this->db->createQueryFactory()
            ->select(
                alias('ability_rules.ability_id', 'ability_id'),
                alias('rules.id', 'rule_id'),
                alias('rules.remark', 'rule_remark'),
                alias('rules.created_at', 'rule_created_at'),
                alias('rules.updated_at', 'rule_updated_at'),
                alias('rule_actions.name', 'rule_action'),
                alias('rule_subjects.name', 'rule_subject')
            )
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
            $rule = RuleMapper::toDomain(new TableData($row, 'rule_'));
            if (! isset($result[$row->ability_id])) {
                $result[$row->ability_id] = [];
            }
            $result[$row->ability_id][] = $rule;
        }

        return $result;
    }
}
