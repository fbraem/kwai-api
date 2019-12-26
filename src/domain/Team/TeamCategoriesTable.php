<?php

namespace Domain\Team;

class TeamCategoriesTable extends \Cake\ORM\Table
{
    public static $registryName = 'TeamCategories';
    public static $tableName = 'team_categories';
    public static $entityClass = 'Domain\Team\TeamCategory';

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
            ->addColumn('start_age', [ 'type' => 'integer' ])
            ->addColumn('end_age', [ 'type' => 'integer' ])
            ->addColumn('competition', [ 'type' => 'boolean' ])
            ->addColumn('gender', [ 'type' => 'integer' ])
            ->addColumn('active', [ 'type' => 'boolean'])
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
