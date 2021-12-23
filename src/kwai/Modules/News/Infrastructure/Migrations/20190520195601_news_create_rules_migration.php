<?php

use Kwai\Core\Infrastructure\Migrations\CreateRulesMigration;

/**
 * Add news rules
 */
class NewsCreateRulesMigration extends CreateRulesMigration
{
    const SUBJECT_NAME = 'news';

    public function up()
    {
        $this->createRules(
            self::SUBJECT_NAME,
            'Seasons',
            [
                'update' => 'Update News',
                'read' => 'Read News',
                'delete' => 'Delete News',
                'create' => 'Create News',
                'manage' => 'Manage News',
            ]
        );
    }

    public function down()
    {
        $this->removeRules(self::SUBJECT_NAME);
    }
}
