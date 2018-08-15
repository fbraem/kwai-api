<?php

namespace Domain\User;

class UserLogsTable extends \Cake\ORM\Table
{
    public static $registryName = 'UserLogs';
    public static $tableName = 'user_logs';
    public static $entityClass = 'Domain\User\User';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('User', [
                'className' => UsersTable::class
            ])
            ->setForeignKey('user_id')
            ->setProperty('user')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('user_id', [ 'type' => 'integer' ])
            ->addColumn('action', [ 'type' => 'string' ])
            ->addColumn('rest', [ 'type' => 'string' ])
            ->addColumn('model_id', [ 'type' => 'integer' ])
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
