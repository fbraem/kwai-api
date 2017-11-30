<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base User Migration
 */
class UserMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('users', ['signed' => false])
            ->addColumn('email', 'string')
            ->addColumn('password', 'string')
            ->addColumn('last_login', 'datetime', ['null' => true])
            ->addColumn('first_name', 'string', ['null' => true])
            ->addColumn('last_name', 'string', ['null' => true])
            ->addColumn('remark', 'text', ['null' => true])
            ->addTimestamps()
            ->create()
        ;

        $this->table('user_logs', ['signed' => false])
            ->addColumn('user_id', 'integer')
            ->addColumn('action', 'string')
            ->addColumn('domain', 'string')
            ->addColumn('model_id', 'integer')
            ->addTimestamps()
            ->create()
        ;
    }

    public function down()
    {
        $this->table('users')->drop();
        $this->table('user_logs')->drop();
    }
}
