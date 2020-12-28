<?php

use Phinx\Migration\AbstractMigration;

/**
 * Add column member_id
 */
class UserMemberMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('users')
            ->addColumn('member_id', 'integer', [ 'null' => true ])
            ->save()
        ;
    }

    public function down()
    {
        $this->table('users')
            ->removeColumn('member_id')
            ->save()
        ;
    }
}
