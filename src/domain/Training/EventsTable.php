<?php

namespace Domain\Training;

class EventsTable extends \Cake\ORM\Table
{
    public static $registryName = 'TrainingEvents';
    public static $tableName = 'training_events';
    public static $entityClass = 'Domain\Training\Event';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('TrainingDefinition', [
                'className' => DefinitionsTable::class
            ])
            ->setForeignKey('training_definition_id')
            ->setProperty('definition')
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
        $this->belongsToMany('TrainingCoaches', [
                'className' => CoachesTable::class,
                'targetForeignKey' => 'training_coach_id',
                'joinTable' => 'training_event_coaches',
                'through' => EventCoachesTable::getTableFromRegistry(),
                'dependent' => true
            ])
            ->setForeignKey('training_event_id')
            ->setProperty('coaches')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('name', [ 'type' => 'string' ])
            ->addColumn('description', [ 'type' => 'text' ])
            ->addColumn('training_definition_id', [ 'type' => 'integer' ])
            ->addColumn('season_id', [ 'type' => 'integer' ])
            ->addColumn('start_date', [ 'type' => 'date' ])
            ->addColumn('start_time', [ 'type' => 'time' ])
            ->addColumn('end_time', [ 'type' => 'time' ])
            ->addColumn('time_zone', [ 'type' => 'string' ])
            ->addColumn('active', [ 'type' => 'boolean' ])
            ->addColumn('cancelled', [ 'type' => 'boolean' ])
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
