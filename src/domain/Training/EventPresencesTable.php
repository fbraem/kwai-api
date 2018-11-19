<?php

namespace Domain\Training;

class EventPresencesTable extends \Cake\ORM\Table
{
    public static $registryName = 'TrainingEventPrecences';
    public static $tableName = 'training_event_presences';
    public static $entityClass = 'Domain\Training\EventPresence';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('TrainingEvent', [
            'className' => EventsTable::class
            ])
            ->setForeignKey('training_event_id')
        ;
        $this->belongsTo('Member', [
            'className' => MembersTable::class
            ])
            ->setForeignKey('member_id')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('training_event_id', ['type' => 'integer'])
            ->addColumn('member_id', ['type' => 'integer'])
            ->addColumn('remark', ['type' => 'text'])
            ->addColumn('created_at', [ 'type' => 'timestamp'])
            ->addColumn('updated_at', [ 'type' => 'timestamp'])
            ->addConstraint(
                'primary',
                [
                    'type' => 'primary',
                    'columns' => [
                        'training_event_id',
                        'member_id'
                    ]
                ]
        );
    }
}
