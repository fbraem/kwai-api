<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base Event Migration
 */
class EventMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('events', ['signed' => false])
            ->addColumn('start_date', 'datetime')
            ->addColumn('end_date', 'datetime')
            ->addColumn('time_zone', 'string')
            ->addColumn('active', 'boolean', ['default' => true])
            ->addColumn('cancelled', 'boolean', ['default' => false])
            ->addColumn('location', 'string', ['null' => true])
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('category_id', 'integer', ['null' => true])
            ->addColumn('user_id', 'integer')
            ->addTimestamps()
            ->create()
        ;

        $this->table('event_contents', ['id' => false, 'primary_key' => ['content_id', 'event_id']])
            ->addColumn('content_id', 'integer')
            ->addColumn('event_id', 'integer')
            ->create()
        ;
    }

    public function down()
    {
        $this->table('events')->drop()->save();
        $this->table('event_contents')->drop()->save();
    }
}
