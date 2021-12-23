<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base Judo Member Migration
 */
class TrainingMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('training_definitions', ['signed' => false])
            ->addColumn('name', 'string')
            ->addColumn('description', 'text')
            ->addColumn('season_id', 'integer', ['null' => true])
            ->addColumn('team_id', 'integer', ['null' => true])
            ->addColumn('weekday', 'integer')
            ->addColumn('start_time', 'time')
            ->addColumn('end_time', 'time')
            ->addColumn('time_zone', 'string')
            ->addColumn('active', 'boolean', ['default' => true])
            ->addColumn('location', 'string', ['null' => true])
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('user_id', 'integer')
            ->addTimestamps()
            ->create()
        ;

        $this->table('training_coaches', ['signed' => false])
            ->addColumn('member_id', 'integer')
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('diploma', 'string', ['null' => true])
            ->addColumn('active', 'boolean', ['default' => true])
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('user_id', 'integer')
            ->addTimestamps()
            ->create()
        ;

        $this->table('training_trainings', ['signed' => false])
            ->addColumn('definition_id', 'integer', ['null' => true])
            ->addColumn('season_id', 'integer', ['null' => true])
            ->addColumn('event_id', 'integer', ['null' => true])
            ->addTimestamps()
            ->create()
        ;

        $this->table('training_teams', ['id' => false, 'primary_key' => ['training_id' , 'team_id']])
            ->addColumn('training_id', 'integer')
            ->addColumn('team_id', 'integer')
            ->addTimestamps()
            ->create()
        ;

        $this->table('training_training_coaches', ['id' => false, 'primary_key' => ['training_id' , 'coach_id']])
            ->addColumn('training_id', 'integer')
            ->addColumn('coach_id', 'integer')
            ->addColumn('coach_type', 'integer')
            ->addColumn('present', 'boolean', ['default' => false])
            ->addColumn('payed', 'boolean', ['default' => false])
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('user_id', 'integer')
            ->addTimestamps()
            ->create()
        ;

        $this->table('training_presences', ['id' => false, 'primary_key' => ['training_id' , 'member_id']])
            ->addColumn('training_id', 'integer')
            ->addColumn('member_id', 'integer')
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('user_id', 'integer')
            ->addTimestamps()
            ->create()
        ;
    }

    public function down()
    {
        $this->table('training_presences')->drop()->save();
        $this->table('training_coaches')->drop()->save();
        $this->table('training_teams')->drop()->save();
        $this->table('training_trainings')->drop()->save();
        $this->table('training_training_coaches')->drop()->save();
        $this->table('training_definitions')->drop()->save();
    }
}
