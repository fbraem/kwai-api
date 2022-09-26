<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base Mail Migration
 */
class MailNullableCreatorMigration extends AbstractMigration
{
    /**
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function up()
    {
        $this->table('mails', ['signed' => false])
            ->changeColumn('user_id', 'integer', ['null' => true])
            ->save()
        ;
    }
}
