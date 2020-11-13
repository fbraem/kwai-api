<?php

/**
 * Add user rules
 */
class UserCreateRulesMigration extends \Domain\CreateRulesMigration
{
    const SUBJECT_USER = 'users';
    const SUBJECT_RULE = 'rules';
    const SUBJECT_ALL = 'all';

    public function up()
    {
        $this->createRules(
            self::SUBJECT_USER,
            'Users',
            [
                'update' => 'Update User',
                'read' => 'Read User',
                'delete' => 'Delete User',
                'create' => 'Create User',
                'manage' => 'Manage User',
            ]
        );
        $this->createRules(
            self::SUBJECT_RULE,
            'Rules',
            [
                'update' => 'Update Rules',
                'read' => 'Read Rules',
                'delete' => 'Delete Rules',
                'create' => 'Create Rules',
                'manage' => 'Manage Rules',
            ]
        );
        $this->createRules(
            self::SUBJECT_ALL,
            'Admin',
            [
                'manage' => 'Manage all'
            ]
        );
        $manageAll = $this->getAdapter()->getConnection()->lastInsertId();

        $data = [
            'name' => 'admin',
            'remark' => _('Administration')
        ];
        $this->table('abilities')->insert($data)->save();
        $ability_id = $this->getAdapter()->getConnection()->lastInsertId();

        $this->table('ability_rules')
            ->insert([
                'ability_id' => $ability_id,
                'rule_id' => $manageAll
            ])
            ->save()
        ;

        $data = [
            'user_id' => 1, // TODO: find the root user?
            'ability_id' => $ability_id
        ];
        $this->table('user_abilities')->insert($data)->save();
    }

    public function down()
    {
        $this->removeRules(self::SUBJECT_USER);
        $this->removeRules(self::SUBJECT_RULE);

        $builder = $this->getQueryBuilder();
        $statement = $builder
            ->select('id')
            ->from('abilities')
            ->where(['name' => 'admin'])
            ->execute();
        $ability_id = $statement->fetch()[0];

        $builder = $this->getQueryBuilder();
        $builder
            ->delete('abilities')
            ->where(['id' => $ability_id])
            ->execute()
        ;
        $builder = $this->getQueryBuilder();
        $builder
            ->delete('ability_rules')
            ->where(['ability_id' => $ability_id])
            ->execute()
        ;
        $builder = $this->getQueryBuilder();
        $builder
            ->delete('user_abilities')
            ->where(['ability_id' => $ability_id])
            ->execute()
        ;
    }
}
