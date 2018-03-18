<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base News Migration
 */
class NewsRemoveUserMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('news_stories')
            ->removeColumn('user_id', 'integer')
            ->save()
        ;
    }

    public function down()
    {
        $this->table('news_stories')
            ->addColumn('user_id', 'integer')
            ->update()
        ;
    }
}
