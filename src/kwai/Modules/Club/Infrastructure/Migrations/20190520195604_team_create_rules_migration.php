<?php

use Kwai\Core\Infrastructure\Migrations\CreateRulesMigration;

/**
 * Add team rules
 */
class TeamCreateRulesMigration extends CreateRulesMigration
{
    const SUBJECT_NAME = 'teams';

    public function up()
    {
        $this->createRules(
            self::SUBJECT_NAME,
            'Teams',
            [
                'update' => 'Update Team',
                'read' => 'Read Team',
                'delete' => 'Delete Team',
                'create' => 'Create Team',
                'manage' => 'Manage Team',
            ]
        );
    }

    public function down()
    {
        $this->removeRules(self::SUBJECT_NAME);
    }
}
