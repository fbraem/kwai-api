<?php

use Phinx\Migration\AbstractMigration;

/**
 * Rename domain column into rest
 */
class UserInvitationsMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('user_invitations', ['signed' => false])
            ->addColumn('email', 'string')
            ->addColumn('token', 'string')
            ->addColumn('expired_at', 'datetime', ['null' => true])
            ->addColumn('remark', 'text', ['null' => true])
            ->addTimestamps()
            ->create()
        ;
    }

    public function down()
    {
        $this->table('user_invitations')->drop();
    }
}
