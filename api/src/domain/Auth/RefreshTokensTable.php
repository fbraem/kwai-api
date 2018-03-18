<?php
namespace Domain\Auth;

class RefreshTokensTable extends \Cake\ORM\Table
{
    public static $registryName = 'RefreshToken';
    public static $tableName = 'oauth_refresh_tokens';
    public static $entityClass = 'Domain\Auth\RefreshToken';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('AccessToken', [
                'className' => \AccessTokensTable::class
            ])
            ->setForeignKey('access_token_id')
            ->setProperty('accesstoken')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('identifier', [ 'type' => 'string' ])
            ->addColumn('access_token_id', [ 'type' => 'integer' ])
            ->addColumn('expiration', [ 'type' => 'timestamp'])
            ->addColumn('revoked', [ 'type' => 'boolean' ])
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
