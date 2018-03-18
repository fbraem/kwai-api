<?php
namespace Domain\Auth;

class AuthorizationCodesTable extends \Cake\ORM\Table
{
    public static $registryName = 'AuthorizationCode';
    public static $tableName = 'oauth_auth_codes';
    public static $entityClass = 'Domain\Auth\AuthorizationCode';

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
        $this->belongsTo('Client', [
                'className' => ClientsTable::class
            ])
            ->setForeignKey('client_id')
            ->setProperty('client')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('identifier', [ 'type' => 'string' ])
            ->addColumn('user_id', [ 'type' => 'integer' ])
            ->addColumn('client_id', [ 'type' => 'integer' ])
            ->addColumn('expiration', [ 'type' => 'timestamp'])
            ->addColumn('redirect_uri', [ 'type' => 'string'])
            ->addColumn('revoked', [ 'type' => 'boolean' ])
            ->addColumn('type', [ 'type' => 'integer' ])
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
