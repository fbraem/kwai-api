<?php

/**
 * Add training rules
 */
class TrainingCreateRulesMigration extends \Domain\CreateRulesMigration
{
    const TRAINING_SUBJECT_NAME = 'training';
    const COACH_SUBJECT_NAME = 'coach';

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
