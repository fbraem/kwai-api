<?php

namespace Domain\User;

class AbilityRulesTable extends \Cake\ORM\Table
{
    public static $registryName = 'AbilityRules';
    public static $tableName = 'ability_rules';
    public static $entityClass = 'Domain\User\AbilityRules';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('Ability', [
            'className' => AbilitiesTable::class
            ])
            ->setForeignKey('ability_id')
        ;
        $this->belongsTo('Rule', [
            'className' => RulesTable::class
            ])
            ->setForeignKey('rule_id')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('ability_id', ['type' => 'integer'])
            ->addColumn('rule_id', ['type' => 'integer'])
            ->addColumn('created_at', [ 'type' => 'timestamp'])
            ->addColumn('updated_at', [ 'type' => 'timestamp'])
            ->addConstraint(
                'primary',
                [
                    'type' => 'primary',
                    'columns' => [
                        'ability_id',
                        'rule_id'
                    ]
                ]
        );
    }
}
