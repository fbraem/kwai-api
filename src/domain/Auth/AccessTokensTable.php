<?php
namespace Domain\Auth;

class AccessTokensTable extends \Cake\ORM\Table
{
    public static $registryName = 'AccessToken';
    public static $tableName = 'oauth_access_tokens';
    public static $entityClass = 'Domain\Auth\AccessToken';

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
            ->addColumn('client_id', [ 'type' => 'integer' ])
            ->addColumn('user_id', [ 'type' => 'integer' ])
            ->addColumn('expiration', [ 'type' => 'timestamp'])
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
