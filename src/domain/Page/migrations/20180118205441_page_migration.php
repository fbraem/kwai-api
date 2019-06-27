<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base News Migration
 */
class PageMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('pages', ['signed' => false])
            ->addColumn('enabled', 'boolean', ['default' => false])
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('category_id', 'integer')
            ->addTimestamps()
            ->create()
        ;

        $this->table('page_contents', ['id' => false, 'primary_key' => ['content_id', 'page_id']])
            ->addColumn('content_id', 'integer')
            ->addColumn('page_id', 'integer')
            ->create()
        ;
    }

    public function down()
    {
        $this->table('page_contents')->drop()->save();
        $this->table('pages')->drop()->save();
    }
}
