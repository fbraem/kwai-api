<?php

namespace Domain\Page;

class PagesTable extends \Cake\ORM\Table
{
    public static $registryName = 'Pages';
    public static $tableName = 'pages';
    public static $entityClass = 'Domain\Page\Page';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('Category', [
                'className' => \Domain\Category\CategoriesTable::class
            ])
            ->setForeignKey('category_id')
            ->setProperty('category')
        ;

        $this->belongsToMany('Contents', [
                'className' => \Domain\Content\ContentsTable::class,
                'joinTable' => 'page_contents',
                'through' => PageContentsTable::getTableFromRegistry(),
                'dependent' => true
            ])
            ->setForeignKey('page_id')
            ->setProperty('contents')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('category_id', [ 'type' => 'integer' ])
            ->addColumn('enabled', [ 'type' => 'boolean' ])
            ->addColumn('remark', [ 'type' => 'text'])
            ->addColumn('priority', [ 'type' => 'integer' ])
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
