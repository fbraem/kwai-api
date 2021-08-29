<?php

use Phinx\Migration\AbstractMigration;

/**
 * A user linked to coach is not the creator but a user coach
 */
class CoachUserMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('coaches', ['signed' => false])
            ->changeColumn('user_id', 'integer', ['null' => true])
            ->update()
        ;
    }

    public function down()
    {
    }
}
