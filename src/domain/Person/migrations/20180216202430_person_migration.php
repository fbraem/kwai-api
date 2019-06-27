<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base Person Migration
 */
class PersonMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('persons', ['signed' => false])
            ->addColumn('lastname', 'string')
            ->addColumn('firstname', 'string')
            ->addColumn('gender', 'integer')
            ->addColumn('active', 'boolean', ['default' => true])
            ->addColumn('birthdate', 'date')
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('contact_id', 'integer', ['null' => true])
            ->addColumn('code', 'string', ['null' => true])
            ->addColumn('nationality_id', 'integer')
            ->addTimestamps()
            ->create()
        ;

        $this->table('children', ['id' => false, 'primary_key' => ['parent_id', 'child_id']])
            ->addColumn('parent_id', 'integer')
            ->addColumn('child_id', 'integer')
            ->addColumn('remark', 'text', ['null' => true])
            ->addTimestamps()
            ->create()
        ;

        $this->table('countries', ['signed' => false])
            ->addColumn('iso_2', 'string', ['limit' => 2])
            ->addColumn('iso_3', 'string', ['limit' => 3])
            ->addColumn('name', 'string')
            ->addTimestamps()
            ->create()
        ;

        // Seeding Country table
        if (($handle = fopen(__DIR__ . "/countries.csv", "r")) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $country = [
                    [
                        'iso_2' => $data[1],
                        'iso_3' => $data[2],
                        'name' => $data[0]
                    ]
                ];
                $this->table('countries')->insert($country)->save();
            }
        }
        fclose($handle);

        $this->table('contacts', ['signed' => false])
            ->addColumn('email', 'string')
            ->addColumn('tel', 'string')
            ->addColumn('mobile', 'string')
            ->addColumn('address', 'string')
            ->addColumn('postal_code', 'string')
            ->addColumn('city', 'string')
            ->addColumn('county', 'string', ['null' => true])
            ->addColumn('country_id', 'integer')
            ->addColumn('remark', 'text', ['null' => true])
            ->addTimestamps()
            ->create()
        ;
    }

    public function down()
    {
        $this->table('persons')->drop()->save();
        $this->table('children')->drop()->save();
        $this->table('countries')->drop()->save();
        $this->table('contacts')->drop()->save();
    }
}
