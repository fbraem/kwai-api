<?php

namespace Domain\Training;

class EventCoachesTable extends \Cake\ORM\Table
{
    public static $registryName = 'TrainingEventCoaches';
    public static $tableName = 'training_event_coaches';
    public static $entityClass = 'Domain\Training\EventCoach';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('TrainingEvent', [
            'className' => EventsTable::class
            ])
            ->setForeignKey('training_event_id')
        ;
        $this->belongsTo('TrainingCoach', [
            'className' => CoachesTable::class
            ])
            ->setForeignKey('training_coach_id')
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
            ->addColumn('training_event_id', ['type' => 'integer'])
            ->addColumn('training_coach_id', ['type' => 'integer'])
            ->addColumn('coach_type', ['type' => 'integer'])
            ->addColumn('present', ['type' => 'boolean'])
            ->addColumn('remark', ['type' => 'text'])
            ->addColumn('user_id', ['type' => 'integer'])
            ->addColumn('created_at', [ 'type' => 'timestamp'])
            ->addColumn('updated_at', [ 'type' => 'timestamp'])
            ->addConstraint(
                'primary',
                [
                    'type' => 'primary',
                    'columns' => [
                        'training_event_id',
                        'training_coach_id'
                    ]
                ]
        );
    }
}
