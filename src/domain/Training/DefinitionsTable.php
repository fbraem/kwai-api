<?php

namespace Domain\Training;

class DefinitionsTable extends \Cake\ORM\Table
{
    public static $registryName = 'TrainingDefinitions';
    public static $tableName = 'training_definitions';
    public static $entityClass = 'Domain\Training\Definition';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->hasMany('TrainingEvents', [
                'className' => EventsTable::class
            ])
            ->setForeignKey('training_definition_id')
            ->setProperty('training_events')
        ;
        $this->belongsTo('Season', [
                'className' => \Domain\Game\SeasonsTable::class
            ])
            ->setForeignKey('season_id')
            ->setProperty('season')
        ;
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
            ->addColumn('name', [ 'type' => 'string' ])
            ->addColumn('description', [ 'type' => 'text' ])
            ->addColumn('season_id', [ 'type' => 'integer' ])
            ->addColumn('weekday', [ 'type' => 'integer' ])
            ->addColumn('start_time', [ 'type' => 'time' ])
            ->addColumn('end_time', [ 'type' => 'time' ])
            ->addColumn('active', [ 'type' => 'boolean' ])
            ->addColumn('location', [ 'type' => 'string' ])
            ->addColumn('remark', [ 'type' => 'text' ])
            ->addColumn('user_id', [ 'type' => 'integer' ])
            ->addColumn('created_at', [ 'type' => 'timestamp' ])
            ->addColumn('updated_at', [ 'type' => 'timestamp' ])
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
