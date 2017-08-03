<?php

namespace User;

use Phoenix\Migration\AbstractMigration;
use Phoenix\Database\Element\Index;

/**
 * Base User Migration
 */
class Migration extends AbstractMigration
{
    protected function up()
    {
        $this->table('users')
            ->addColumn('email', 'string')
            ->addColumn('password', 'string')
            ->addColumn('last_login', 'datetime', ['null' => true])
            ->addColumn('first_name', 'string', ['null' => true])
            ->addColumn('last_name', 'string', ['null' => true])
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->create()
        ;
    }

    protected function down()
    {
        $this->table('users')
            ->drop();
    }
}
