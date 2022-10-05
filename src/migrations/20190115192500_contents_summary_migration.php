<?php

use Phinx\Migration\AbstractMigration;

/**
 * Summary -> not null, content -> null
 */
class ContentsSummaryMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('contents')
            ->changeColumn('summary', 'text', ['null' => false])
            ->changeColumn('content', 'text', ['null' => true])
            ->save()
        ;
    }

    public function down()
    {
        $this->table('contents')
            ->changeColumn('summary', 'text', ['null' => true])
            ->changeColumn('content', 'text', ['null' => true])
            ->save()
        ;
    }
}
