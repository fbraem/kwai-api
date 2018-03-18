<?php

namespace Domain\Person;

class ContactsTable extends \Cake\ORM\Table
{
    public static $registryName = 'Contacts';
    public static $tableName = 'contacts';
    public static $entityClass = 'Domain\Person\Contact';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();
        $this->belongsTo('Country', [
                'className' => CountriesTable::class
            ])
            ->setForeignKey('country_id')
            ->setProperty('country');
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('email', [ 'type' => 'string' ])
            ->addColumn('tel', [ 'type' => 'string' ])
            ->addColumn('mobile', [ 'type' => 'string' ])
            ->addColumn('address', [ 'type' => 'string' ])
            ->addColumn('postal_code', [ 'type' => 'string' ])
            ->addColumn('city', [ 'type' => 'string' ])
            ->addColumn('county', [ 'type' => 'string' ])
            ->addColumn('remark', [ 'type' => 'text' ])
            ->addColumn('created_at', [ 'type' => 'timestamp'])
            ->addColumn('updated_at', [ 'type' => 'timestamp'])
            ->addConstraint(
                'primary',
                [
                    'type' => 'primary',
                    'columns' => [
                        'id'
                    ]
                ]
        );
    }
}
