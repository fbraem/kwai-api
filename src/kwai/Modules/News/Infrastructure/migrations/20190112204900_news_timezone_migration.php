<?php

use Phinx\Migration\AbstractMigration;

/**
 * Change Timezone columns
 */
class NewsTimezoneMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('news_stories')
            ->renameColumn('publish_date_timezone', 'timezone')
            ->removeColumn('featured_end_date_timezone')
            ->removeColumn('end_date_timezone')
            ->save()
        ;
    }

    public function down()
    {
        $this->table('news_stories')
            ->renameColumn('timezone', 'publish_date_timezone')
            ->addColumn('featured_end_date_timezone', 'datetime', ['null' => true])
            ->addColumn('end_date_timezone', 'datetime', ['null' => true])
            ->update()
        ;
    }
}
