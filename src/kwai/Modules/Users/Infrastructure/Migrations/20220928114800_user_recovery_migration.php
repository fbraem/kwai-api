<?php

use Kwai\Core\Infrastructure\Dependencies\Settings;
use Phinx\Migration\AbstractMigration;

/**
 * Base User Migration
 */
class UserRecoveryMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('user_recoveries', ['signed' => false])
            ->addColumn('user_id', 'integer')
            ->addColumn('uuid', 'string')
            ->addColumn('expired_at', 'datetime')
            ->addColumn('expired_at_timezone', 'string')
            ->addColumn('confirmed_at', 'datetime', ['null' => true])
            ->addColumn('remark', 'string', ['null' => true])
            ->addTimestamps()
            ->create()
        ;
    }
}
