<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base News Migration
 */
class NewsMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('news_categories', ['signed' => false])
            ->addColumn('name', 'string')
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('user_id', 'integer')
            ->addColumn('original_image_path', 'string', ['null' => true])
            ->addColumn('image_path', 'string', ['null' => true])
            ->addTimestamps()
            ->create()
        ;
        $this->table('news_stories', ['signed' => false])
            ->addColumn('title', 'string')
            ->addColumn('content', 'text')
            ->addColumn('summary', 'text', ['null' => true])
            ->addColumn('enabled', 'boolean', ['default' => false])
            ->addColumn('featured', 'integer', ['default' => 0])
            ->addColumn('featured_end_date', 'datetime', ['null' => true])
            ->addColumn('publish_date', 'datetime', ['null' => true])
            ->addColumn('end_date', 'datetime', ['null' => true])
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
        $this->table('news_categories')->drop();
    }
}
