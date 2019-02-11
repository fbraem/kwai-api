<?php

namespace Domain\Training;

class TrainingCoachesTable extends \Cake\ORM\Table
{
    public static $registryName = 'TrainingTrainingCoaches';
    public static $tableName = 'training_training_coaches';
    public static $entityClass = 'Domain\Training\TrainingCoach';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('Training', [
            'className' => TrainingsTable::class
            ])
            ->setForeignKey('training_id')
        ;
        $this->belongsTo('Coach', [
            'className' => CoachesTable::class
            ])
            ->setForeignKey('coach_id')
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
            ->addColumn('training_id', ['type' => 'integer'])
            ->addColumn('coach_id', ['type' => 'integer'])
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
                        'training_id',
                        'coach_id'
                    ]
                ]
        );
    }
}
