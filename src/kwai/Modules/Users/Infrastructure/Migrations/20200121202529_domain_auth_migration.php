<?php

use Phinx\Migration\AbstractMigration;

class DomainAuthMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('oauth_access_tokens')
            ->removeColumn('client_id')
            ->removeColumn('type')
            ->save()
        ;
    }

    public function down()
    {
        $this->table('oauth_access_tokens', ['signed' => false])
            ->addColumn('client_id', 'integer')
            ->addColumn('type', 'integer', ['signed' => false, 'default' => 1])
            ->save()
        ;
    }
}
