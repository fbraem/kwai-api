<?php

/**
 * Add club rules
 */
class ClubCreateRulesMigration extends \Domain\CreateRulesMigration
{
    const SUBJECT_NAME = 'club';

    public function up()
    {
        $this->createRules(
            self::SUBJECT_NAME,
            'Clubs',
            [
                'update' => 'Update Club',
                'read' => 'Read Club',
                'delete' => 'Delete Club',
                'create' => 'Create Club',
                'manage' => 'Manage Club',
            ]
        );
    }

    public function down()
    {
        $this->removeRules(self::SUBJECT_NAME);
    }
}
