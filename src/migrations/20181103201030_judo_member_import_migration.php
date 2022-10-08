<?php

use Phinx\Migration\AbstractMigration;

/**
 * Judo Member Migration
 */
class JudoMemberImportMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('sport_judo_members', ['signed' => false])
            ->addColumn('import_id', 'integer', ['null' => true])
            ->addColumn('active', 'boolean', ['default' => true])
            ->save()
        ;
    }

    public function down()
    {
        $this->table('sport_judo_members')
            ->removeColumn('import_id')
            ->removeColumn('active')
            ->save()
        ;
    }
}
