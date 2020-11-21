<?php

use Phinx\Migration\AbstractMigration;

/**
 * Server side CASL tables
 */
class UserRulesMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('rule_actions', ['signed' => false])
            ->addColumn('name', 'string')
            ->addColumn('remark', 'text', ['null' => true])
            ->addTimestamps()
            ->create()
        ;

        $data = [
            [
                'name' => 'read',
                'remark' => _('Read action')
            ],
            [
                'name' => 'create',
                'remark' => _('Create action')
            ],
            [
                'name' => 'update',
                'remark' => _('Update action')
            ],
            [
                'name' => 'delete',
                'remark' => _('Delete action')
            ],
            [
                'name' => 'manage',
                'remark' => _('Manage action')
            ]
        ];
        $this->table('rule_actions')->insert($data)->save();

        $this->table('rule_subjects', ['signed' => false])
            ->addColumn('name', 'string')
            ->addColumn('remark', 'text', ['null' => true])
            ->addTimestamps()
            ->create()
        ;

        $data = [
            'name' => 'all',
            'remark' => _('Matches all subjects')
        ];
        $this->table('rule_subjects')->insert($data)->save();

        $this->table('rules', ['signed' => false])
            ->addColumn('name', 'string')
            ->addColumn('action_id', 'integer')
            ->addColumn('subject_id', 'integer')
            ->addColumn('owner', 'boolean', ['default' => false])
            ->addColumn('remark', 'text', ['null' => true])
            ->addTimestamps()
            ->create()
        ;

        $this->table('abilities', ['signed' => false])
            ->addColumn('name', 'string')
            ->addColumn('remark', 'text', ['null' => true])
            ->addTimestamps()
            ->create()
        ;

        $this->table('ability_rules', ['id' => false, 'primary_key' => ['ability_id' , 'rule_id']])
            ->addColumn('ability_id', 'integer')
            ->addColumn('rule_id', 'integer')
            ->addTimestamps()
            ->create()
        ;

        $this->table('user_abilities', ['id' => false, 'primary_key' => ['user_id' , 'ability_id']])
            ->addColumn('user_id', 'integer')
            ->addColumn('ability_id', 'integer')
            ->addTimestamps()
            ->create()
        ;
    }

    public function down()
    {
        $this->table('user_abilities')->drop()->save();
        $this->table('ability_rules')->drop()->save();
        $this->table('abilities')->drop()->save();
        $this->table('rules')->drop()->save();
        $this->table('rule_subjects')->drop()->save();
        $this->table('rule_actions')->drop()->save();
    }
}
