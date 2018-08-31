<?php

namespace Domain\User;

class UserInvitationsTable extends \Cake\ORM\Table
{
    public static $registryName = 'UserInvitations';
    public static $tableName = 'user_invitations';
    public static $entityClass = 'Domain\User\UserInvitation';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('email', [ 'type' => 'string' ])
            ->addColumn('name', [ 'type' => 'string' ])
            ->addColumn('token', [ 'type' => 'string' ])
            ->addColumn('expired_at', [ 'type' => 'datetime' ])
            ->addColumn('expired_at_timezone', [ 'type' => 'string' ])
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
