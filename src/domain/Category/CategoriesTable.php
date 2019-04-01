<?php

namespace Domain\Category;

class CategoriesTable extends \Cake\ORM\Table
{
    public static $registryName = 'Categories';
    public static $tableName = 'categories';
    public static $entityClass = 'Domain\Category\Category';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('User', [
                'className' => \Domain\User\UsersTable::class
            ])
            ->setForeignKey('user_id')
            ->setProperty('user')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('user_id', [ 'type' => 'integer' ])
            ->addColumn('name', [ 'type' => 'string' ])
            ->addColumn('description', [ 'type' => 'text' ])
            ->addColumn('remark', [ 'type' => 'text'])
            ->addColumn('short_description', [ 'type' => 'string'])
            ->addColumn('slug', [ 'type' => 'string'])
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
