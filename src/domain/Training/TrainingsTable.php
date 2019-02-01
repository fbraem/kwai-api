<?php

namespace Domain\Training;

class TrainingsTable extends \Cake\ORM\Table
{
    public static $registryName = 'Trainings';
    public static $tableName = 'training_trainings';
    public static $entityClass = 'Domain\Training\Training';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('TrainingDefinition', [
                'className' => DefinitionsTable::class
            ])
            ->setForeignKey('definition_id')
            ->setProperty('definition')
        ;
        $this->belongsTo('Season', [
                'className' => \Domain\Game\SeasonsTable::class
            ])
            ->setForeignKey('season_id')
            ->setProperty('season')
        ;
        $this->belongsTo('Event', [
                'className' => \Domain\Event\EventsTable::class
            ])
            ->setForeignKey('event_id')
            ->setProperty('event')
        ;
        $this->belongsToMany('TrainingCoaches', [
                'className' => CoachesTable::class,
                'targetForeignKey' => 'coach_id',
                'joinTable' => 'training_training_coaches',
                'through' => TrainingCoachesTable::getTableFromRegistry(),
                'dependent' => true
            ])
            ->setForeignKey('training_id')
            ->setProperty('coaches')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('definition_id', [ 'type' => 'integer' ])
            ->addColumn('season_id', [ 'type' => 'integer' ])
            ->addColumn('event_id', [ 'type' => 'integer' ])
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
