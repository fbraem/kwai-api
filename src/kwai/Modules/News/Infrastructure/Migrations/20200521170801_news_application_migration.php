<?php

use Phinx\Migration\AbstractMigration;

/**
 * Add news rules
 */
class NewsApplicationMigration extends AbstractMigration
{
    const SUBJECT_NAME = 'news';

    public function up()
    {
        $this->table('news_stories')
            ->renameColumn('category_id', 'application_id')
            ->save()
        ;
    }

    public function down()
    {
        $this->table('news_stories')
            ->renameColumn('application_id', 'category_id')
            ->save()
        ;
    }
}
