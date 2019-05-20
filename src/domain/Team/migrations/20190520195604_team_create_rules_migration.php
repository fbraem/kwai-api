<?php

/**
 * Add team rules
 */
class TeamCreateRulesMigration extends \Domain\CreateRulesMigration
{
    const SUBJECT_NAME = 'team';

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
