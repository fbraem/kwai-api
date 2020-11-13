<?php

use Phinx\Migration\AbstractMigration;

/**
 * Rename categories to applications and add some columns
 */
class ApplicationMigration extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('categories');
        $table->rename('applications');
        $table
            ->addColumn('news', 'boolean', ['default' => true])
            ->addColumn('pages', 'boolean', ['default' => true])
            ->addColumn('events', 'boolean', ['default' => true])
            ->addColumn('weight', 'integer', ['default' => 0])
            ->removeColumn('user_id')
            ->renameColumn('name', 'title')
            ->renameColumn('app', 'name')
            ->save()
        ;

        if ($this->getEnvironment() == 'test') {
            $data = [
                [
                    'title' => 'Test Application',
                    'description' => 'This application is used when running tests',
                    'short_description' => 'The test environment',
                    'name' => 'test',
                    'news' => true,
                    'pages' => true,
                    'events' => true,
                    'weight' => 1
                ]
            ];
            $this->table('applications')->insert($data)->save();
        }
    }

    public function down()
    {
        $table = $this->table('applications');
        $table
            ->removeColumn('news')
            ->removeColumn('pages')
            ->removeColumn('events')
            //->removeColumn('weight')
            ->renameColumn('name', 'app')
            ->renameColumn('title', 'name')
            ->addColumn('user_id', 'integer')
            ->save()
        ;

        $table->rename('categories')
            ->save();
    }
}
