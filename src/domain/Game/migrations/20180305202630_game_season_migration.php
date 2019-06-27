<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base Judo Member Migration
 */
class GameSeasonMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('seasons', ['signed' => false])
            ->addColumn('name', 'string')
            ->addColumn('start_date', 'date')
            ->addColumn('end_date', 'date')
            ->addColumn('remark', 'text', ['null' => true])
            ->addTimestamps()
            ->create()
        ;
    }

    public function down()
    {
        $this->table('seasons')->drop()->save();
    }
}
