<?php

namespace Domain\Team;

class TeamsTable extends \Cake\ORM\Table
{
    public static $registryName = 'Teams';
    public static $tableName = 'teams';
    public static $entityClass = 'Domain\Team\Team';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('TeamType', [
                'className' => TeamTypesTable::class
            ])
            ->setForeignKey('team_type_id')
            ->setProperty('team_type')
        ;
        $this->belongsTo('Season', [
                'className' => \Domain\Game\SeasonsTable::class
            ])
            ->setForeignKey('season_id')
            ->setProperty('season')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('name', [ 'type' => 'string' ])
            ->addColumn('season_id', [ 'type' => 'integer' ])
            ->addColumn('team_type_id', [ 'type' => 'integer' ])
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
