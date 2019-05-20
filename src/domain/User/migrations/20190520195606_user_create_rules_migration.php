<?php

/**
 * Add user rules
 */
class UserCreateRulesMigration extends \Domain\CreateRulesMigration
{
    const SUBJECT_NAME = 'user';

    public function up()
    {
        $this->createRules(
            self::SUBJECT_NAME,
            'Users',
            [
                'update' => 'Update User',
                'read' => 'Read User',
                'delete' => 'Delete User',
                'create' => 'Create User',
                'manage' => 'Manage User',
            ]
        );
    }

    public function down()
    {
        $this->removeRules(self::SUBJECT_NAME);
    }
}
