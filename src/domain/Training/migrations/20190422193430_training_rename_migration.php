<?php

use Phinx\Migration\AbstractMigration;

/**
 * Rename some tables
 */
class TrainingRenameMigration extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('training_coaches');
        $table->rename('coaches');

        $table = $this->table('training_training_coaches');
        $table->rename('training_coaches');

        $table = $this->table('training_trainings');
        $table->rename('trainings');
    }

    public function down()
    {
        $table = $this->table('training_coaches');
        $table->rename('training_training_coaches');

        $table = $this->table('coaches');
        $table->rename('training_coaches');

        $table = $this->table('trainings');
        $table->rename('training_trainings');
    }
}
