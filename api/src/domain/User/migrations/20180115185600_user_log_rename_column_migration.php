<?php

use Phinx\Migration\AbstractMigration;

/**
 * Rename domain column into rest
 */
class UserLogRenameColumnMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('user_logs')->renameColumn('domain', 'rest');
    }

    public function down()
    {
        $this->table('user_logs')->renameColumn('rest', 'domain');
    }
}
