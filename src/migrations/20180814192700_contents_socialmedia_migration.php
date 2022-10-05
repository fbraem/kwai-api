<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base Contents Migration
 */
class ContentsSocialmediaMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('contents')
            ->addColumn('social_media', 'text', ['null' => true])
            ->update()
        ;
    }

    public function down()
    {
        $this->table('contents')
            ->removeColumn('social_media')
            ->save()
        ;
    }
}
