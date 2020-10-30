<?php

namespace Domain\User;

class UserAbilitiesTable extends \Cake\ORM\Table
{
    public static $registryName = 'UserAbilities';
    public static $tableName = 'user_abilities';
    public static $entityClass = 'Domain\User\UserAbility';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('User', [
            'className' => UsersTable::class
            ])
            ->setForeignKey('user_id')
        ;
        $this->belongsTo('Ability', [
            'className' => AbilitiesTable::class
            ])
            ->setForeignKey('ability_id')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
        ->addColumn('user_id', ['type' => 'integer'])
            ->addColumn('ability_id', ['type' => 'integer'])
            ->addColumn('created_at', [ 'type' => 'timestamp'])
            ->addColumn('updated_at', [ 'type' => 'timestamp'])
            ->addConstraint(
                'primary',
                [
                    'type' => 'primary',
                    'columns' => [
                        'user_id',
                        'ability_id'
                    ]
                ]
        );
    }
}
