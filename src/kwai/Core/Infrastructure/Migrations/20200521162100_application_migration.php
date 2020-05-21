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
            ->save()
        ;

        $table->rename('categories');
    }
}
