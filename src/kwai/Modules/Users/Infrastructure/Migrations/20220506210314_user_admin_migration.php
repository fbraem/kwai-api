<?php

use Phinx\Migration\AbstractMigration;

/**
 * User Migration
 * Add admin column
 */
class UserAdminMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('users')
            ->addColumn('admin', 'boolean', ['default' => false])
            ->save()
        ;
    }

    public function down()
    {
        $this->table('users')
            ->removeColumn('admin')
            ->save()
        ;
    }
}
