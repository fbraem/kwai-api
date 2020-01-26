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

    private $table;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->table = new AbilityTable();
    }

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
                        'rules.remark' => 'rule_remark',
                        'rules.created_at' => 'rule_created_at',
                        'rules.updated_at' => 'rule_updated_at',
                        'rule_actions.name' => 'rule_action',
                        'rule_subjects.name' => 'rule_subject',
                    ]);
                })
                ->all();
            ;
            $rules = [];
            foreach ($rows as $row) {
                $rule = RuleMapper::toDomain(new TableData($row, 'rule_'));
                $rules[] = $rule;
            }
            $ability->abilities_rules = $rules;

            return AbilityMapper::toDomain(
                new TableData($ability, $this->table->prefix())
            );
        }
        throw new NotFoundException('Ability');
    }
}
