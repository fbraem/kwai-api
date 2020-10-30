<?php

namespace Domain\User;

class RuleActionsTable extends \Cake\ORM\Table
{
    public static $registryName = 'RuleActions';
    public static $tableName = 'rule_actions';
    public static $entityClass = 'Domain\User\RuleAction';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();
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
