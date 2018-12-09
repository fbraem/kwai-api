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

        $this->table('training_events', ['signed' => false])
            ->addColumn('name', 'string', ['null' => true])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('training_definition_id', 'integer', ['null' => true])
            ->addColumn('season_id', 'integer')
            ->addColumn('start_date', 'date')
            ->addColumn('start_time', 'time')
            ->addColumn('end_time', 'time')
            ->addColumn('time_zone', 'string')
            ->addColumn('active', 'boolean', ['default' => true])
            ->addColumn('cancelled', 'boolean', ['default' => false])
            ->addColumn('location', 'string', ['null' => true])
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('user_id', 'integer')
            ->addTimestamps()
            ->create()
        ;

        $this->table('training_event_teams', ['id' => false, 'primary_key' => ['training_event_id' , 'team_id']])
            ->addColumn('training_event_id', 'integer')
            ->addColumn('team_id', 'integer')
            ->addTimestamps()
            ->create()
        ;

        $this->table('training_event_coaches', ['id' => false, 'primary_key' => ['training_event_id' , 'training_coach_id']])
            ->addColumn('training_event_id', 'integer')
            ->addColumn('training_coach_id', 'integer')
            ->addColumn('coach_type', 'integer')
            ->addColumn('present', 'boolean')
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('user_id', 'integer')
            ->addTimestamps()
            ->create()
        ;

        $this->table('training_event_presences', ['id' => false, 'primary_key' => ['training_event_id' , 'member_id']])
            ->addColumn('training_event_id', 'integer')
            ->addColumn('member_id', 'integer')
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('user_id', 'integer')
            ->addTimestamps()
            ->create()
        ;
    }

    public function down()
    {
        $this->table('training_event_presences')->drop();
        $this->table('training_event_coaches')->drop();
        $this->table('training_event_teams')->drop();
        $this->table('training_events')->drop();
        $this->table('training_coaches')->drop();
        $this->table('training_definitions')->drop();
    }
}
