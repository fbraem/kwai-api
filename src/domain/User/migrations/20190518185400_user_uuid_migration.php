<?php

use Phinx\Migration\AbstractMigration;

/**
 * Server side CASL tables
 */
class UserUUIDMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('users')
            ->addColumn('uuid', 'string', ['after' => 'remark'])
            ->save()
        ;

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
                ->set('uuid', bin2hex(random_bytes(16)))
                ->where(['id' => $row['id']])
                ->execute()
            ;
        }
    }

    public function down()
    {
        $this->table('users')
            ->removeColumn('uuid')
            ->save()
        ;
    }
}
