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

use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\Ability;
use Kwai\Modules\Users\Domain\Rule;
use Kwai\Modules\Users\Repositories\AbilityRepository;
use Kwai\Modules\Users\Infrastructure\Mappers\AbilityMapper;
use Kwai\Modules\Users\Infrastructure\Mappers\RuleMapper;
use Kwai\Modules\Users\Infrastructure\AbilityTable;

use Opis\Database\Database;

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

    /**
     * Get the ability with the given id.
     * @param  int    $id
     * @return Entity<Ability>
     */
    public function getById(int $id): Entity
    {
        $ability = $this->db->from($this->table->from())
            ->where('id')->is($id)
            ->select(function ($include) {
                $include->columns($this->table->alias());
            })
            ->first()
        ;
        if ($ability) {
            $rules = $this->getRulesForAbilities([$id]);
            $ability->abilities_rules = $rules[$id];

            return AbilityMapper::toDomain(
                new TableData($ability, $this->table->prefix())
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
        $rows = $this->db->from('ability_rules')
            ->where('ability_id')->in($abilities)
            ->leftJoin(
                'rules',
                function ($join) {
                    $join->on('ability_rules.rule_id', 'rules.id');
                }
            )
            ->leftJoin(
                'rule_actions',
                function ($join) {
                    $join->on('rules.action_id', 'rule_actions.id');
                }
            )
            ->leftJoin(
                'rule_subjects',
                function ($join) {
                    $join->on('rules.subject_id', 'rule_subjects.id');
                }
            )
            ->select(function ($include) {
                $include->columns([
                    'ability_rules.ability_id' => 'ability_id',
                    'rules.id' => 'rule_id',
                    'rules.remark' => 'rule_remark',
                    'rules.created_at' => 'rule_created_at',
                    'rules.updated_at' => 'rule_updated_at',
                    'rule_actions.name' => 'rule_action',
                    'rule_subjects.name' => 'rule_subject',
                ]);
            })
            ->all();
        ;

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
