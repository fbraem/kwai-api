<?php

use Kwai\Core\Infrastructure\Migrations\CreateRulesMigration;

/**
 * Add event rules
 */
class EventCreateRulesMigration extends CreateRulesMigration
{
    const SUBJECT_NAME = 'events';

    public function up()
    {
        $this->createRules(
            self::SUBJECT_NAME,
            'Events',
            [
                'update' => 'Update Event',
                'read' => 'Read Event',
                'delete' => 'Delete Event',
                'create' => 'Create Event',
                'manage' => 'Manage Event',
            ]
        );
    }

    public function down()
    {
        $this->removeRules(self::SUBJECT_NAME);
    }
}
