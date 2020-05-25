<?php

use Phinx\Migration\AbstractMigration;

/**
 * Rename categories to applications and add some columns
 */
class ApplicationMigration extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('categories');
        $table->rename('applications');
        $table
            ->addColumn('news', 'boolean', ['default' => true])
            ->addColumn('pages', 'boolean', ['default' => true])
            ->addColumn('events', 'boolean', ['default' => true])
            ->removeColumn('user_id')
            ->renameColumn('name', 'title')
            ->renameColumn('app', 'name')
            ->save()
        ;
    }

    public function down()
    {
        $table = $this->table('applications');
        $table
            ->removeColumn('news')
            ->removeColumn('pages')
            ->removeColumn('events')
            ->renameColumn('name', 'app')
            ->renameColumn('title', 'name')
            ->addColumn('user_id', 'integer')
            ->save()
        ;

        $table->rename('categories')
            ->save();
    }
}
