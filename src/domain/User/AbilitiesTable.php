<?php

namespace Domain\User;

class AbilitiesTable extends \Cake\ORM\Table
{
    public static $registryName = 'Abilities';
    public static $tableName = 'abilities';
    public static $entityClass = 'Domain\User\Ability';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsToMany('Rules', [
                'className' => RulesTable::class,
                'targetForeignKey' => 'rule_id',
                'joinTable' => 'ability_rules',
                'through' => AbilityRulesTable::getTableFromRegistry(),
                'dependent' => true
            ])
            ->setForeignKey('ability_id')
            ->setProperty('rules')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('name', [ 'type' => 'string' ])
            ->addColumn('remark', [ 'type' => 'text' ])
            ->addColumn('created_at', [ 'type' => 'timestamp'])
            ->addColumn('updated_at', [ 'type' => 'timestamp'])
            ->addConstraint(
                'primary',
                [
                    'type' => 'primary',
                    'columns' => [
                        'id'
                    ]
                ]
        );
    }
}
