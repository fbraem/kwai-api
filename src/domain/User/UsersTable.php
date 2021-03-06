<?php

namespace Domain\User;

class UsersTable extends \Cake\ORM\Table
{
    public static $registryName = 'Users';
    public static $tableName = 'users';
    public static $entityClass = 'Domain\User\User';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsToMany('Abilities', [
                'className' => AbilitiesTable::class,
                'targetForeignKey' => 'ability_id',
                'joinTable' => 'user_abilities',
                'through' => UserAbilitiesTable::getTableFromRegistry(),
                'dependent' => true
            ])
            ->setForeignKey('user_id')
            ->setProperty('abilities')
        ;
        $this->belongsToMany('Logs', [
                'className' => UserLogsTable::class,
                'dependent' => true
            ])
            ->setForeignKey('user_id')
            ->setProperty('logs')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('email', [ 'type' => 'string' ])
            ->addColumn('password', [ 'type' => 'string' ])
            ->addColumn('last_login', [ 'type' => 'timestamp'])
            ->addColumn('last_name', [ 'type' => 'string' ])
            ->addColumn('first_name', [ 'type' => 'string' ])
            ->addColumn('remark', [ 'type' => 'text' ])
            ->addColumn('uuid', ['type' => 'string'])
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
