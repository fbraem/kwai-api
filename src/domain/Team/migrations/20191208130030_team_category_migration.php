<?php

use Phinx\Migration\AbstractMigration;

/**
 * Rename team_types into team_categories
 */
class TeamCategoryMigration extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('team_types');
        $table->rename('team_categories');
        $table->save();
        $table = $this->table('teams');
        $table->renameColumn('team_type_id', 'team_category_id');
        $table->save();
    }

    public function down()
    {
        $table = $this->table('team_categories');
        $table->rename('team_types');
        $table->save();
        $table = $this->table('teams');
        $table->renameColumn('team_category_id', 'team_type_id');
        $table->save();
    }
}
