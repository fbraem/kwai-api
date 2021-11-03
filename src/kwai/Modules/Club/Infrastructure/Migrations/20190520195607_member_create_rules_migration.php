<?php

use Kwai\Core\Infrastructure\Migrations\CreateRulesMigration;

/**
 * Add member rules
 */
class MemberCreateRulesMigration extends CreateRulesMigration
{
    const SUBJECT_NAME = 'members';

    public function up()
    {
        $this->createRules(
            self::SUBJECT_NAME,
            'Members',
            [
                'update' => 'Update Member',
                'read' => 'Read Member',
                'delete' => 'Delete Member',
                'create' => 'Create Member',
                'manage' => 'Manage Member',
            ]
        );
    }

    public function down()
    {
        $this->removeRules(self::SUBJECT_NAME);
    }
}
