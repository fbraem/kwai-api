<?php

use Phinx\Migration\AbstractMigration;

/**
 * Add slug column
 */
class CategorySlugMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('categories', ['signed' => false])
            ->addColumn('slung', 'string')
            ->save()
        ;
    }

    public function down()
    {
        $this->table('categories')
            ->removeColumn('slug')
            ->save();
    }
}
