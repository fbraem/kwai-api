<?php

use Phinx\Migration\AbstractMigration;

/**
 * Migration to move training event data to trainings table
 * and to move the event_contents table to training_contents
 */
class TrainingEventsMigration extends AbstractMigration
{
    public function up()
    {
/*
        $this->table('trainings', ['signed' => false])
            ->addColumn('start_date', 'datetime', ['default' => '1900-01-01'])
            ->addColumn('end_date', 'datetime', ['default' => '1900-01-01'])
            ->addColumn('time_zone', 'string')
            ->addColumn('active', 'boolean', ['default' => true])
            ->addColumn('cancelled', 'boolean', ['default' => false])
            ->addColumn('location', 'string', ['null' => true])
            ->update()
        ;
        $this->table('training_contents', ['id' => false, 'primary_key' => ['training_id', 'locale']])
            ->addColumn('training_id', 'integer')
            ->addColumn('locale', 'string')
            ->addColumn('format', 'string')
            ->addColumn('title', 'string')
            ->addColumn('content', 'text', [ 'null' => true ])
            ->addColumn('summary', 'text')
            ->addColumn('user_id', 'integer')
            ->addTimestamps()
            ->create()
        ;
*/

        $eventsQuery = $this->getQueryBuilder();
        $eventsQuery->select([
            'events.start_date',
            'events.end_date',
            'events.time_zone',
            'events.active',
            'events.cancelled',
            'events.location'
        ])
        ->from('events')
        ->join([
            't' => [
                'table' => 'trainings',
                'conditions' => [
                    't.event_id = events.id'
                ]
            ]
        ]);
        $events = $eventsQuery->execute();
        foreach ($events as $event) {
            $update = $this->getQueryBuilder();
            $update
                ->update('trainings')
                ->set([
                    'start_date' => $event['start_date'],
                    'end_date' => $event['end_date'],
                    'time_zone' => $event['time_zone'],
                    'active' => $event['active'],
                    'cancelled' => $event['cancelled'],
                    'location' => $event['location']
                ])
                ->execute()
            ;
        }

        $contentsQuery = $this->getQueryBuilder();
        $contentsQuery
            ->select([
                'training_id' => 'event_id',
                'locale',
                'format',
                'title',
                'content',
                'summary',
                'user_id',
                'created_at' => 'c.created_at',
                'updated_at' => 'c.updated_at'
            ])
            ->from('event_contents')
            ->join([
                'c' => [
                    'table' => 'contents',
                    'conditions' => [
                        'c.id = event_contents.content_id'
                    ]
                ]
            ])
        ;
        $builder = $this->getQueryBuilder();
        $builder
            ->insert([
                'training_id',
                'locale',
                'format',
                'title',
                'content',
                'summary',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->into('training_contents')
            ->values($contentsQuery)
            ->execute()
        ;

        $this->table('trainings')
            ->removeColumn('event_id')
            ->changeColumn('start_date', 'datetime', ['null' => false])
            ->changeColumn('end_date', 'datetime', ['null' => false])
            ->update()
        ;
    }

    public function down()
    {
        $this->table('training_contents')
            ->drop()
            ->save()
        ;
        $this->table('trainings')
            ->removeColumn('start_date')
            ->removeColumn('end_date')
            ->removeColumn('time_zone')
            ->removeColumn('active')
            ->removeColumn('cancelled')
            ->removeColumn('location')
            ->update()
        ;
    }
}
