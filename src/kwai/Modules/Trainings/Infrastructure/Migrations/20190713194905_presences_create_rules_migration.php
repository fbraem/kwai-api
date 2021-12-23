<?php

use Kwai\Core\Infrastructure\Migrations\CreateRulesMigration;

/**
 * Add training rules
 */
class PresencesCreateRulesMigration extends CreateRulesMigration
{
    const SUBJECT_NAME = 'presences';

    public function up()
    {
        $this->createRules(
            self::SUBJECT_NAME,
            'Training Presences',
            [
                'update' => 'Update Presence',
                'read' => 'Read Presence',
                'delete' => 'Delete Presence',
                'create' => 'Create Presence',
                'manage' => 'Manage Presence',
            ]
        );
    }

    public function down()
    {
        $this->removeRules(self::_SUBJECT_NAME);
    }
}
