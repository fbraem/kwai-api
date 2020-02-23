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
            ->addColumn('tag', 'string')
            ->addColumn('uuid', 'string')
            ->addColumn('sender_email', 'string')
            ->addColumn('sender_name', 'string')
            ->addColumn('subject', 'string')
            ->addColumn('html_body', 'text', ['null' => true])
            ->addColumn('text_body', 'text')
            ->addColumn('sent_time', 'datetime', ['null' => true])
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('user_id', 'integer')
            ->addTimestamps()
            ->create()
        ;
        $this->table('mail_recipients', ['signed' => false])
            ->addColumn('mail_id', 'integer')
            ->addColumn('type', 'integer')
            ->addColumn('email', 'string')
            ->addColumn('name', 'string', ['null' => true])
            ->create()
        ;
    }

    public function down()
    {
        $this->table('mails')->drop()->save();
        $this->table('mail_recipients')->drop()->save();
    }
}
