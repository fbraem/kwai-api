<?php

namespace Domain\Training;

class EventTeamsTable extends \Cake\ORM\Table
{
    public static $registryName = 'TrainingEventTeams';
    public static $tableName = 'training_event_teams';
    public static $entityClass = 'Domain\Training\EventTeam';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('TrainingEvent', [
            'className' => EventsTable::class
            ])
            ->setForeignKey('training_event_id')
        ;
        $this->belongsTo('Team', [
            'className' => TeamsTable::class
            ])
            ->setForeignKey('team_id')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('training_event_id', ['type' => 'integer'])
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
