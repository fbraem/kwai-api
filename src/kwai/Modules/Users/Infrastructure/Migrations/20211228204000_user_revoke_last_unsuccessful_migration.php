<?php

use Phinx\Migration\AbstractMigration;

/**
 * Add column member_id
 */
class UserRevokeLastUnsuccessfulMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('users')
            ->addColumn('revoked', 'boolean', ['default' => false])
            ->addColumn('last_unsuccessful_login', 'datetime', ['null' => true])
            ->save()
        ;
    }

    public function down()
    {
        $this->table('users')
            ->removeColumn('last_unsuccessful_login')
            ->removeColumn('revoked')
            ->save()
        ;
    }
}
