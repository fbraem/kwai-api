<?php

namespace Domain\Training;

class CoachesTable extends \Cake\ORM\Table
{
    public static $registryName = 'Coaches';
    public static $tableName = 'coaches';
    public static $entityClass = 'Domain\Training\Coach';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('Member', [
                'className' => \Judo\Domain\Member\MembersTable::class
            ])
            ->setForeignKey('member_id')
            ->setProperty('member')
        ;
        $this->belongsTo('User', [
                'className' => \Domain\User\UsersTable::class
            ])
            ->setForeignKey('user_id')
            ->setProperty('user')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('member_id', [ 'type' => 'integer' ])
            ->addColumn('description', [ 'type' => 'text' ])
            ->addColumn('diploma', [ 'type' => 'string' ])
            ->addColumn('remark', [ 'type' => 'text' ])
            ->addColumn('active', [ 'type' => 'boolean' ])
            ->addColumn('user_id', [ 'type' => 'integer' ])
            ->addColumn('created_at', [ 'type' => 'timestamp' ])
            ->addColumn('updated_at', [ 'type' => 'timestamp' ])
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
