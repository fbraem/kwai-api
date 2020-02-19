<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base Mail Migration
 */
class MailMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('mails', ['signed' => false])
            ->addColumn('uuid', 'string')
            ->addColumn('from', 'string')
            ->addColumn('to', 'string')
            ->addColumn('cc', 'string')
            ->addColumn('bcc', 'string')
            ->addColumn('body', 'text')
            ->addColumn('sent_time', 'datetime', ['null' => true])
            ->addColumn('remark', 'text', ['null' => true])
            ->addTimestamps()
            ->create()
        ;
    }

    public function down()
    {
        $this->table('mails')->drop()->save();
    }
}
