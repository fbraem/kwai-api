<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base Judo Member Migration
 */
class ClubMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('clubs', ['signed' => false])
            ->addColumn('name', 'string')
            ->addColumn('license', 'string', ['null' => true])
            ->addColumn('active', 'boolean', ['default' => true])
            ->addColumn('remark', 'text', ['null' => true])
            ->addTimestamps()
            ->create()
        ;

        $this->table('club_members', ['id' => false, 'primary_key' => ['club_id', 'member_id']])
            ->addColumn('club_id', 'integer')
            ->addColumn('member_id', 'integer')
            ->addColumn('active', 'boolean', ['default' => true])
            ->addTimestamps()
            ->create()
        ;
    }

    public function down()
    {
        $this->table('club_members')->drop();
        $this->table('clubs')->drop();
    }
}
