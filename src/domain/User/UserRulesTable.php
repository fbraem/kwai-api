<?php

namespace Domain\User;

class UserRulesTable extends \Cake\ORM\Table
{
    public static $registryName = 'UserRules';
    public static $tableName = 'user_rules';
    public static $entityClass = 'Domain\User\UserRule';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('User', [
            'className' => UsersTable::class
            ])
            ->setForeignKey('user_id')
        ;
        $this->belongsTo('RuleGroup', [
            'className' => RuleGroupssTable::class
            ])
            ->setForeignKey('rule_group_id')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
        ->addColumn('user_id', ['type' => 'integer'])
            ->addColumn('rule_group_id', ['type' => 'integer'])
            ->addColumn('created_at', [ 'type' => 'timestamp'])
            ->addColumn('updated_at', [ 'type' => 'timestamp'])
            ->addConstraint(
                'primary',
                [
                    'type' => 'primary',
                    'columns' => [
                        'user_id',
                        'rule_group_id'
                    ]
                ]
        );
    }
}
