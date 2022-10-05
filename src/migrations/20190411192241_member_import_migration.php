<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base News Migration
 */
class MemberImportMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('member_imports', ['signed' => false])
            ->addColumn('filename', 'text', ['null' => true])
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('user_id', 'integer')
            ->addTimestamps()
            ->create()
        ;
    }

    public function down()
    {
        $this->table('member_imports')->drop()->save();
    }
}
