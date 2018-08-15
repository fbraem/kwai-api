<?php

namespace Domain\Content;

class ContentsTable extends \Cake\ORM\Table
{
    public static $registryName = 'Contents';
    public static $tableName = 'contents';
    public static $entityClass = 'Domain\Content\Content';

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
            ->addColumn('locale', [ 'type' => 'string' ])
            ->addColumn('format', [ 'type' => 'string'])
            ->addColumn('title', [ 'type' => 'string'])
            ->addColumn('content', [ 'type' => 'text'])
            ->addColumn('summary', [ 'type' => 'text'])
            ->addColumn('created_at', [ 'type' => 'timestamp'])
            ->addColumn('updated_at', [ 'type' => 'timestamp'])
            ->addColumn('social_media', [ 'type' => 'text'])
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
