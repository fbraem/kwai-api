<?php

/**
 * Add category rules
 */
class CategoryCreateRulesMigration extends \Domain\CreateRulesMigration
{
    const SUBJECT_NAME = 'categories';

    public function up()
    {
        $this->createRules(
            self::SUBJECT_NAME,
            'Categories',
            [
                'update' => 'Update Category',
                'read' => 'Read Category',
                'delete' => 'Delete Category',
                'create' => 'Create Category',
                'manage' => 'Manage Category',
            ]
        );
    }

    public function down()
    {
        $this->removeRules(self::SUBJECT_NAME);
    }
}
