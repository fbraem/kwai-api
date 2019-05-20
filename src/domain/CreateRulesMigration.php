<?php

namespace Domain;

use Phinx\Migration\AbstractMigration;

/**
 * Add app column
 */
class CreateRulesMigration extends AbstractMigration
{
    public function createRules($subject, $remark, $rules)
    {
        $builder = $this->getQueryBuilder();
        $statement = $builder
            ->select('*')
            ->from('rule_actions')
            ->execute();
        $actions = array_reduce(
            $statement->fetchAll('assoc'),
            function ($result, $item) {
                $result[$item['name']] = $item;
                return $result;
            },
            array()
        );

        $builder = $this->getQueryBuilder();
        $statement = $builder
            ->insert(['name', 'remark'])
            ->into('rule_subjects')
            ->values([
                'name' => $subject,
                'remark' => $remark
            ])
            ->execute()
        ;
        $subject_id = $statement->lastInsertId('rule_subjects', 'id');

        $data = [];
        foreach ($rules as $action => $name) {
            $data[] = [
                'name' => $name,
                'subject_id' => $subject_id,
                'action_id' => $actions[$action]['id'],
            ];
        }
        $this->table('rules')
            ->insert($data)
            ->save();
    }

    public function removeRules($subject)
    {
        $builder = $this->getQueryBuilder();
        $statement = $builder
            ->select('id')
            ->from('rule_subjects')
            ->where(['name' => $subject])
            ->execute();
        $this->subject_id = $statement->fetch()[0];

        $builder = $this->getQueryBuilder();
        $builder
            ->delete('rules')
            ->where(['subject_id' => $this->subject_id])
            ->execute()
        ;

        $builder = $this->getQueryBuilder();
        $builder
            ->delete('rule_subjects')
            ->where(['id' => $this->subject_id])
            ->execute()
        ;
    }
}
