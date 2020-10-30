<?php

namespace Domain\User;

class RulesTable extends \Cake\ORM\Table
{
    public static $registryName = 'Rules';
    public static $tableName = 'rules';
    public static $entityClass = 'Domain\User\Rule';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('RuleAction', [
                'className' => RuleActionsTable::class
            ])
            ->setForeignKey('action_id')
            ->setProperty('action')
        ;
        $this->belongsTo('RuleSubject', [
                'className' => RuleSubjectsTable::class
            ])
            ->setForeignKey('subject_id')
            ->setProperty('subject')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('name', [ 'type' => 'string' ])
            ->addColumn('action_id', [ 'type' => 'integer' ])
            ->addColumn('subject_id', [ 'type' => 'integer' ])
            ->addColumn('owner', [ 'type' => 'boolean' ])
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
