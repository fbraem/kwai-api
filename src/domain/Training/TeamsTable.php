<?php

namespace Domain\Training;

class TeamsTable extends \Cake\ORM\Table
{
    public static $registryName = 'TrainingTeams';
    public static $tableName = 'training_teams';
    public static $entityClass = 'Domain\Training\TrainingTeam';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('Training', [
            'className' => TrainingsTable::class
            ])
            ->setForeignKey('training_id')
        ;
        $this->belongsTo('Team', [
            'className' => \Domain\Team\TeamsTable::class
            ])
            ->setForeignKey('team_id')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('training_id', ['type' => 'integer'])
            ->addColumn('team_id', ['type' => 'integer'])
            ->addColumn('created_at', [ 'type' => 'timestamp'])
            ->addColumn('updated_at', [ 'type' => 'timestamp'])
            ->addConstraint(
                'primary',
                [
                    'type' => 'primary',
                    'columns' => [
                        'training_event_id',
                        'team_id'
                    ]
                ]
        );
    }
}
