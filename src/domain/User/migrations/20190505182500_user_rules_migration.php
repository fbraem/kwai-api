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

        $this->table('rule_groups', ['signed' => false])
            ->addColumn('name', 'string')
            ->addColumn('remark', 'text', ['null' => true])
            ->addTimestamps()
            ->create();
        ;
        $data = [
            'name' => 'admin',
            'remark' => _('Administration Rules')
        ];
        $this->table('rule_groups')->insert($data)->save();

        $this->table('rule_group_items', ['id' => false, 'primary_key' => ['rule_group_id' , 'rule_id']])
            ->addColumn('rule_group_id', 'integer')
            ->addColumn('rule_id', 'integer')
            ->addTimestamps()
            ->create()
        ;

        $this->table('user_rules', ['id' => false, 'primary_key' => ['user_id' , 'rule_group_id']])
            ->addColumn('user_id', 'integer')
            ->addColumn('rule_group_id', 'integer')
            ->addTimestamps()
            ->create()
        ;
    }

    public function down()
    {
        $this->table('user_rules')->drop();
        $this->table('rule_group_items')->drop();
        $this->table('rule_groups')->drop();
        $this->table('rules')->drop();
        $this->table('rule_subjects')->drop();
        $this->table('rule_actions')->drop();
    }
}
