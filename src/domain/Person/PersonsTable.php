<?php

namespace Domain\Person;

class PersonsTable extends \Cake\ORM\Table
{
    public static $registryName = 'Persons';
    public static $tableName = 'persons';
    public static $entityClass = 'Domain\Person\Person';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('Nationality', [
                'className' => CountriesTable::class
            ])
            ->setForeignKey('nationality_id')
            ->setProperty('nationality')
        ;

        $this->belongsTo('Contact', [
            'className' => ContactsTable::class
            ])
            ->setForeignKey('contact_id')
            ->setProperty('contact')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('lastname', [ 'type' => 'string' ])
            ->addColumn('firstname', [ 'type' => 'string' ])
            ->addColumn('gender', [ 'type' => 'integer' ])
            ->addColumn('active', [ 'type' => 'boolean' ])
            ->addColumn('birthdate', [ 'type' => 'date' ])
            ->addColumn('code', [ 'type' => 'string' ])
            ->addColumn('nationality_id', [ 'type' => 'integer' ])
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
