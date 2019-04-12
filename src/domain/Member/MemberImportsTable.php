<?php

namespace Domain\Member;

class MemberImportsTable extends \Cake\ORM\Table
{
    public static $registryName = 'MemberImpors';
    public static $tableName = 'member_imports';
    public static $entityClass = 'Domain\Member\MemberImport';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

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
            ->addColumn('user_id', [ 'type' => 'integer' ])
            ->addColumn('filename', [ 'type' => 'string'])
            ->addColumn('remark', [ 'type' => 'text'])
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
