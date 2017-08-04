<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base User Migration
 */
class UserMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('users')
            ->addColumn('email', 'string')
            ->addColumn('password', 'string')
            ->addColumn('last_login', 'datetime', ['null' => true])
            ->addColumn('first_name', 'string')
            ->addColumn('last_name', 'string')
            ->addColumn('remark', 'text')
            ->addTimestamps(null, 'modified_at')
            ->create();
    }

    public function down()
    {
        $this->table('users')->drop();
    }
}
