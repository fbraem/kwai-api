<?php

use Phinx\Migration\AbstractMigration;

/**
 * Remove rest column + add status column
 */
class UserLogStatusColumnMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('user_logs')
            ->addColumn('status', 'integer')
            ->removeColumn('rest')
            ->save();
    }

    public function down()
    {
        $this->table('user_logs')
            ->addColumn('rest', 'string')
            ->removeColumn('status')
            ->save()
        ;
    }
}
