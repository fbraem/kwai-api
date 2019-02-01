<?php

namespace Domain\Event;

class EventContentsTable extends \Cake\ORM\Table
{
    public static $registryName = 'EventContents';
    public static $tableName = 'event_contents';
    public static $entityClass = 'Domain\Event\EventContent';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable(false);

        $this->belongsTo('Event', [
            'className' => EventsTable::class
            ])
            ->setForeignKey('event_id')
        ;
        $this->belongsTo('Content', [
            'className' => \Domain\Content\ContentsTable::class
            ])
            ->setForeignKey('content_id')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('event_id', ['type' => 'integer'])
            ->addColumn('content_id', ['type' => 'integer'])
            ->addConstraint(
                'primary',
                [
                    'type' => 'primary',
                    'columns' => [
                        'event_id',
                        'content_id'
                    ]
                ]
        );
    }
}
