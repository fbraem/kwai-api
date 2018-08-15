<?php

namespace Domain\Person;

class CountriesTable extends \Cake\ORM\Table
{
    public static $registryName = 'Countries';
    public static $tableName = 'countries';
    public static $entityClass = 'Domain\Person\Country';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();
    }

    protected function initializeSchema($schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('iso_2', [ 'type' => 'string' ])
            ->addColumn('iso_3', [ 'type' => 'string' ])
            ->addColumn('name', [ 'type' => 'string' ])
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
