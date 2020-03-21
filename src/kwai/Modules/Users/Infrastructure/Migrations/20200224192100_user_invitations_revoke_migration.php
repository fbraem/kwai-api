<?php

use Phinx\Migration\AbstractMigration;

/**
 * Add column revoked
 */
class UserInvitationsRevokeMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('user_invitations')
            ->addColumn('revoked', 'boolean', [ 'default' => false ])
            ->addColumn('confirmed_at', 'datetime', [ 'default' => false ])
            ->renameColumn('token', 'uuid')
            ->save()
        ;
    }

    public function down()
    {
        $this->table('user_invitations')
            ->removeColumn('revoked')
            ->removeColumn('confirmed_at')
            ->renameColumn('uuid', 'token')
            ->save()
        ;
    }
}
