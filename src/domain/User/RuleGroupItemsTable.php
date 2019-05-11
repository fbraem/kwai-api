<?php

namespace Domain\User;

class RuleGroupItemsTable extends \Cake\ORM\Table
{
    public static $registryName = 'RuleGroupItems';
    public static $tableName = 'rule_group_items';
    public static $entityClass = 'Domain\User\RuleGroupItems';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('RuleGroup', [
            'className' => RuleGroupssTable::class
            ])
            ->setForeignKey('rule_group_id')
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
            ->addColumn('rule_group_id', ['type' => 'integer'])
            ->addColumn('rule_id', ['type' => 'integer'])
            ->addColumn('created_at', [ 'type' => 'timestamp'])
            ->addColumn('updated_at', [ 'type' => 'timestamp'])
            ->addConstraint(
                'primary',
                [
                    'type' => 'primary',
                    'columns' => [
                        'rule_group_id',
                        'rule_id'
                    ]
                ]
        );
    }
}
