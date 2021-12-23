<?php

use Kwai\Core\Infrastructure\Migrations\CreateRulesMigration;

/**
 * Add person rules
 */
class PersonCreateRulesMigration extends CreateRulesMigration
{
    const SUBJECT_NAME = 'persons';

    public function up()
    {
        $this->createRules(
            self::SUBJECT_NAME,
            'Persons',
            [
                'update' => 'Update Person',
                'read' => 'Read Person',
                'delete' => 'Delete Person',
                'create' => 'Create Person',
                'manage' => 'Manage Person',
            ]
        );
    }

    public function down()
    {
        $this->removeRules(self::SUBJECT_NAME);
    }
}
