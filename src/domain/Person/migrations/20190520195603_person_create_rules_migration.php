<?php

/**
 * Add person rules
 */
class PersonCreateRulesMigration extends \Domain\CreateRulesMigration
{
    const SUBJECT_NAME = 'person';

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
