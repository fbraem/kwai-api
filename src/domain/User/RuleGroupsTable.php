<?php

namespace Domain\User;

class RuleGroupsTable extends \Cake\ORM\Table
{
    public static $registryName = 'RuleGroups';
    public static $tableName = 'rule_groups';
    public static $entityClass = 'Domain\User\RuleGroup';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsToMany('Rules', [
                'className' => RulesTable::class,
                'targetForeignKey' => 'rule_id',
                'joinTable' => 'rule_group_items',
                'through' => RuleGroupItemsTable::getTableFromRegistry(),
                'dependent' => true
            ])
            ->setForeignKey('rule_group_id')
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
