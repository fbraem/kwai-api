<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base News Migration
 */
class NewsMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('news_stories', ['signed' => false])
            ->addColumn('enabled', 'boolean', ['default' => false])
            ->addColumn('featured', 'integer', ['default' => 0])
            ->addColumn('featured_end_date', 'datetime', ['null' => true])
            ->addColumn('featured_end_date_timezone', 'string', ['null' => true])
            ->addColumn('publish_date', 'datetime')
            ->addColumn('publish_date_timezone', 'string')
            ->addColumn('end_date', 'datetime', ['null' => true])
            ->addColumn('end_date_timezone', 'string', ['null' => true])
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('category_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addTimestamps()
            ->create()
        ;
    }

    public function down()
    {
        $this->table('news_stories')->drop();
    }
}
