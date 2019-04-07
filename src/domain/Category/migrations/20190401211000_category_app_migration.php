<?php

use Phinx\Migration\AbstractMigration;

/**
 * Add app column
 */
class CategoryAppMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('categories', ['signed' => false])
            ->addColumn('app', 'string')
            ->save()
        ;
    }

    public function down()
    {
        $this->table('categories')
            ->removeColumn('app')
            ->save();
    }
}
