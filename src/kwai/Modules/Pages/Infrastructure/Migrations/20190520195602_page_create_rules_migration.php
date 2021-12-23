<?php

use Kwai\Core\Infrastructure\Migrations\CreateRulesMigration;

/**
 * Add page rules
 */
class PageCreateRulesMigration extends CreateRulesMigration
{
    const SUBJECT_NAME = 'page';

    public function up()
    {
        $this->createRules(
            self::SUBJECT_NAME,
            'Pages',
            [
                'update' => 'Update Page',
                'read' => 'Read Page',
                'delete' => 'Delete Page',
                'create' => 'Create Page',
                'manage' => 'Manage Page',
            ]
        );
    }

    public function down()
    {
        $this->removeRules(self::SUBJECT_NAME);
    }
}
