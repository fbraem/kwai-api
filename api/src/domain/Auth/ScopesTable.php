<?php
namespace Domain\Auth;

class ScopesTable extends \Cake\ORM\Table
{
    public static $registryName = 'Scope';
    public static $tableName = 'oauth_scopes';
    public static $entityClass = 'Domain\Auth\Scope';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('identifier', [ 'type' => 'string' ])
            ->addColumn('description', [ 'type' => 'string' ])
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
