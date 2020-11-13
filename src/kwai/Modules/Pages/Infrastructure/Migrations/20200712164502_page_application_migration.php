<?php

use Phinx\Migration\AbstractMigration;

/**
 * Rename category_id into application_id
 */
class PageApplicationMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('pages')
            ->renameColumn('category_id', 'application_id')
            ->save()
        ;
    }

    public function down()
    {
        $this->table('pages')
            ->renameColumn('application_id', 'category_id')
            ->save()
        ;
    }
}
