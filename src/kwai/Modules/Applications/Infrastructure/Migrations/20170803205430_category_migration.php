<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base Category Migration
 */
class CategoryMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('categories', ['signed' => false])
            ->addColumn('name', 'string')
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('user_id', 'integer')
            ->addTimestamps()
            ->create()
        ;
    }

    public function down()
    {
        $this->table('categories')->drop()->save();
    }
}
