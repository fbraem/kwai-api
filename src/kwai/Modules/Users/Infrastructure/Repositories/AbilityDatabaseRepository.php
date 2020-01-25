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

use Kwai\Modules\Users\Repositories\AbilityRepository;
use Kwai\Modules\Users\Infrastructure\Mappers\AbilityMapper;
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

    private $table;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->table = new AbilityTable();
    }

    // SELECT rules.id as rule_id, rule_subjects.name as subject, rule_actions.name as action from ability_rules
    // left join rules on rules.id = ability_rules.rule_id
    // left join rule_actions on rules.action_id = rule_actions.id
    // left join rule_subjects on rules.subject_id = rule_subjects.id
    // where ability_id = 1
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
            $rows = $this->db->from('ability_rules')
                ->where('ability_id')->is($id)
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
                        'rules.id' => 'rule_id',
                        'rule_actions.name' => 'rule_action',
                        'rule_subjects.name' => 'rule_subject',
                    ]);
                })
                ->all();
            ;
            $rules = [];
            foreach ($rows as $row) {
                $rule = new TableData($row, 'rule_');
                $rules[$rule->id] = $rule;
            }
            $ability->abilities_rules = $rules;

            return AbilityMapper::toDomain(
                new TableData($ability, $this->table->prefix())
            );
        }
        throw new NotFoundException('Ability');
    }
}
