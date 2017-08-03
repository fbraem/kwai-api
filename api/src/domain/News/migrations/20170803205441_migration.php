<?php

use Phoenix\Migration\AbstractMigration;

/**
 * Base News Migration
 */
class Migration extends AbstractMigration
{
    protected function up()
    {
        $this->table('categories')
            ->addColumn('name', 'string')
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('user_id', 'integer')
            ->addColumn('original_image_path', 'string', ['null' => true])
            ->addColumn('image_path', 'string', ['null' => true])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->create()
        ;
        $this->table('news')
            ->addColumn('title', 'string')
            ->addColumn('content', 'text')
            ->addColumn('summary', 'text', ['null' => true])
            ->addColumn('enabled', 'boolean', ['default' => false])
            ->addColumn('featured', 'integer', ['default' => 0])
            ->addColumn('featured_end_date', 'datetime', ['null' => true])
            ->addColumn('publish_date', 'datetime', ['null' => true])
            ->addColumn('end_date', 'datetime', ['null' => true])
            ->addColumn('original_image_header_path', 'string', ['null' => true])
            ->addColumn('image_header_path', 'string', ['null' => true])
            ->addColumn('original_image_overview_path', 'string', ['null' => true])
            ->addColumn('image_overview_path', 'string', ['null' => true])
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('category_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->create()
        ;
    }

    protected function down()
    {
        $this->table('news')
            ->drop()
        ;
    }
}
