<?php
namespace Domain\Auth;

class ClientsTable extends \Cake\ORM\Table
{
    public static $registryName = 'Client';
    public static $tableName = 'oauth_clients';
    public static $entityClass = 'Domain\Auth\Client';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('name', [ 'type' => 'string' ])
            ->addColumn('identifier', [ 'type' => 'string' ])
            ->addColumn('secret', [ 'type' => 'string' ])
            ->addColumn('redirect_uri', [ 'type' => 'string' ])
            ->addColumn('status', [ 'type' => 'integer' ])
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
