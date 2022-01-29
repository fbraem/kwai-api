<?php

use Phinx\Migration\AbstractMigration;

/**
 * Add column member_id
 */
class NewRulesMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('abilities')
            ->rename('roles')
            ->addColumn('description', 'string', ['null' => true])
            ->save()
        ;

        $this->table('ability_rules')
            ->rename('role_rules')
            ->renameColumn('ability_id', 'role_id')
            ->save()
        ;

        $this->table('rules')
            ->addColumn('permission', 'integer', ['default' => 0])
            ->changeColumn('action_id', 'integer', ['null' => true])
            ->save()
        ;

        $this->table('user_abilities')
            ->rename('user_roles')
            ->renameColumn('ability_id', 'role_id')
            ->save()
        ;
    }

    public function down()
    {
        $this->table('user_roles')
            ->rename('user_abilities')
            ->renameColumn('role_id', 'ability_id')
            ->save()
        ;
        $this->table('rules')
            ->removeColumn('permission')
            ->save()
        ;
        $this->table('role_rules')
            ->rename('ability_rules')
            // ->renameColumn('role_id', 'ability_id')
            ->save()
        ;
        $this->table('roles')
            ->removeColumn('description')
            ->rename('abilities')
            ->save()
        ;
    }
}
