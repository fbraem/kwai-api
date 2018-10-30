<?php

namespace Domain\Game;

class SeasonsTable extends \Cake\ORM\Table
{
    public static $registryName = 'Seasons';
    public static $tableName = 'seasons';
    public static $entityClass = 'Domain\Game\Season';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->hasMany('Teams', [
                'className' => \Domain\Team\TeamsTable::class
        ]);
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('name', [ 'type' => 'string' ])
            ->addColumn('start_date', [ 'type' => 'date' ])
            ->addColumn('end_date', [ 'type' => 'date' ])
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
