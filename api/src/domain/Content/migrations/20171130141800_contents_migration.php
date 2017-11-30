<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base Contents Migration
 */
class ContentsMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('contents', ['signed' => false])
            ->addColumn('locale', 'string')
            ->addColumn('format', 'string')
            ->addColumn('title', 'string')
            ->addColumn('content', 'text')
            ->addColumn('summary', 'text', ['null' => true])
            ->addColumn('user_id', 'integer')
            ->addTimestamps()
            ->create()
        ;
        $this->table('contentables', ['id' => false, 'primary_key' => ['content_id', 'contentable_id', 'contentable_type']])
            ->addColumn('content_id', 'integer')
            ->addColumn('contentable_id', 'integer')
            ->addColumn('contentable_type', 'string')
            ->create()
        ;
    }

    public function down()
    {
        $this->table('contents')->drop();
        $this->table('contentables')->drop();
    }
}
