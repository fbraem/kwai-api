<?php

use Phinx\Migration\AbstractMigration;

/**
 * Add ids to presences and training_training_coaches
 */
class TrainingIdMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('training_training_coaches')->drop();
        $this->table('training_training_coaches', ['signed' => false])
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

        $this->table('training_presences')->drop();
        $this->table('training_presences', ['signed' => false])
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
        $this->table('training_presences')->drop();
        $this->table('training_presences', ['id' => false, 'primary_key' => ['training_id' , 'member_id']])
            ->addColumn('training_id', 'integer')
            ->addColumn('member_id', 'integer')
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('user_id', 'integer')
            ->addTimestamps()
            ->create()
        ;
        $this->table('training_training_coaches')->drop();
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
    }
}
