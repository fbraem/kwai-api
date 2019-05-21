<?php

/**
 * Add user rules
 */
class UserCreateRulesMigration extends \Domain\CreateRulesMigration
{
    const SUBJECT_USER = 'users';
    const SUBJECT_RULE = 'rules';

    public function up()
    {
        $this->createRules(
            self::SUBJECT_USER,
            'Users',
            [
                'update' => 'Update User',
                'read' => 'Read User',
                'delete' => 'Delete User',
                'create' => 'Create User',
                'manage' => 'Manage User',
            ]
        );
        $this->createRules(
            self::SUBJECT_RULE,
            'Rules',
            [
                'update' => 'Update Rules',
                'read' => 'Read Rules',
                'delete' => 'Delete Rules',
                'create' => 'Create Rules',
                'manage' => 'Manage Rules',
            ]
        );
    }

    public function down()
    {
        $this->removeRules(self::SUBJECT_USER);
        $this->removeRules(self::SUBJECT_RULE);
    }
}
