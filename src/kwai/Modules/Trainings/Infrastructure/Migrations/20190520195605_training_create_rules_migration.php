<?php

use Kwai\Core\Infrastructure\Migrations\CreateRulesMigration;

/**
 * Add training rules
 */
class TrainingCreateRulesMigration extends CreateRulesMigration
{
    const TRAINING_SUBJECT_NAME = 'trainings';
    const COACH_SUBJECT_NAME = 'coaches';

    public function up()
    {
        $this->createRules(
            self::TRAINING_SUBJECT_NAME,
            'Trainings',
            [
                'update' => 'Update Training',
                'read' => 'Read Training',
                'delete' => 'Delete Training',
                'create' => 'Create Training',
                'manage' => 'Manage Training',
            ]
        );
        $this->createRules(
            self::COACH_SUBJECT_NAME,
            'Coaches',
            [
                'update' => 'Update Coach',
                'read' => 'Read Coach',
                'delete' => 'Delete Coach',
                'create' => 'Create Coach',
                'manage' => 'Manage Coach',
            ]
        );
    }

    public function down()
    {
        $this->removeRules(self::TRAINING_SUBJECT_NAME);
        $this->removeRules(self::COACH_SUBJECT_NAME);
    }
}
