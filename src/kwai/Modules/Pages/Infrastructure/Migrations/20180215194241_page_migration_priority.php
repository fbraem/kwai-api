<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base News Migration
 */
class PageMigrationPriority extends AbstractMigration
{
    public function up()
    {
        $this->table('pages')
            ->addColumn('priority', 'integer', ['after' => 'category_id'])
            ->update()
        ;
    }

    public function down()
    {
        $this->table('pages')->removeColumn('priority')->save();
    }
}
