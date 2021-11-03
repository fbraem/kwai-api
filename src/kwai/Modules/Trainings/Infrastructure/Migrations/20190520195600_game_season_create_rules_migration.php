<?php

/**
 * Add season rules
 */
class GameSeasonCreateRulesMigration extends \Domain\CreateRulesMigration
{
    const SUBJECT_NAME = 'seasons';

    public function up()
    {
        $this->createRules(
            self::SUBJECT_NAME,
            'Seasons',
            [
                'update' => 'Update Season',
                'read' => 'Read Season',
                'delete' => 'Delete Season',
                'create' => 'Create Season',
                'manage' => 'Manage Season',
            ]
        );
    }

    public function down()
    {
        $this->removeRules(self::SUBJECT_NAME);
    }
}
