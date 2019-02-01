<?php

namespace Domain\Event;

class EventsTable extends \Cake\ORM\Table
{
    public static $registryName = 'Events';
    public static $tableName = 'events';
    public static $entityClass = 'Domain\Event\Event';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('Category', [
                'className' => \Domain\Category\CategoriesTable::class
            ])
            ->setForeignKey('category_id')
            ->setProperty('category')
        ;

        $this->belongsTo('User', [
                'className' => \Domain\User\UsersTable::class
            ])
            ->setForeignKey('user_id')
            ->setProperty('user')
        ;

        $this->belongsToMany('Contents', [
                'className' => \Domain\Content\ContentsTable::class,
                'joinTable' => 'event_contents',
                'through' => EventContentsTable::getTableFromRegistry(),
                'dependent' => true
            ])
            ->setForeignKey('event_id')
            ->setProperty('contents')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('category_id', [ 'type' => 'integer' ])
            ->addColumn('active', [ 'type' => 'boolean' ])
            ->addColumn('cancelled', [ 'type' => 'boolean' ])
            ->addColumn('start_date', [ 'type' => 'datetime' ])
            ->addColumn('end_date', [ 'type' => 'datetime' ])
            ->addColumn('time_zone', [ 'type' => 'string' ])
            ->addColumn('location', [ 'type' => 'string' ])
            ->addColumn('remark', [ 'type' => 'text'])
            ->addColumn('created_at', [ 'type' => 'timestamp'])
            ->addColumn('updated_at', [ 'type' => 'timestamp'])
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
