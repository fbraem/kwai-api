<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base Judo Member Migration
 */
class TeamMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('team_types', ['signed' => false])
            ->addColumn('name', 'string')
            ->addColumn('start_age', 'integer', ['null' => true])
            ->addColumn('end_age', 'integer', ['null' => true])
            ->addColumn('competition', 'boolean', ['default' => false])
            ->addColumn('gender', 'integer', ['default' => 0]) // 0 = Ignore, 1 = Male, 2 = Female
            ->addColumn('active', 'boolean', ['default' => true])
            ->addColumn('remark', 'text', ['null' => true])
            ->addTimestamps()
            ->create()
        ;

        $this->table('teams', ['signed' => false])
            ->addColumn('name', 'string', ['null' => true])
            ->addColumn('season_id', 'integer', ['null' => true])
            ->addColumn('team_type_id', 'integer', ['null' => true])
            ->addColumn('active', 'boolean', ['default' => true])
            ->addColumn('remark', 'text', ['null' => true])
            ->addTimestamps()
            ->create()
        ;

        $this->table('team_members', ['id' => false, 'primary_key' => ['team_id', 'member_id']])
            ->addColumn('team_id', 'integer')
            ->addColumn('member_id', 'integer')
            ->addColumn('active', 'boolean', ['default' => true])
            ->addTimestamps()
            ->create()
        ;
    }

    public function down()
    {
        $this->table('team_members')->drop()->save();
        $this->table('teams')->drop()->save();
        $this->table('team_types')->drop()->save();
    }
}
