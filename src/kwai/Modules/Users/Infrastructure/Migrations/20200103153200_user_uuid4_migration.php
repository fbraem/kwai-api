<?php

use Phinx\Migration\AbstractMigration;
use Ramsey\Uuid\Uuid;

/**
 * Change UUIDs of the users into UUID v4.
 */
class UserUUID4Migration extends AbstractMigration
{
    public function up()
    {
        $builder = $this->getQueryBuilder();
        $rows = $builder
            ->select('*')
            ->from('users')
            ->execute()
            ->fetchAll('assoc')
        ;
        foreach ($rows as $row) {
            $builder = $this->getQueryBuilder();
            $builder->update('users')
                ->set('uuid', Uuid::uuid4())
                ->where(['id' => $row['id']])
                ->execute()
            ;
        }
    }

    public function down()
    {
    }
}
