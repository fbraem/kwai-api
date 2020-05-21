<?php

use Phinx\Migration\AbstractMigration;

/**
 * Add short_description column
 */
class CategoryDescriptionMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('categories', ['signed' => false])
            ->addColumn('short_description', 'string')
            ->save()
        ;
    }

    public function down()
    {
        $this->table('categories')
            ->removeColumn('short_description')
            ->save();
    }
}
