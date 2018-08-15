<?php

namespace Domain\Page;

class PageContentsTable extends \Cake\ORM\Table
{
    public static $registryName = 'PageContents';
    public static $tableName = 'page_contents';
    public static $entityClass = 'Domain\Page\PageContent';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable(false);

        $this->belongsTo('Page', [
            'className' => PagesTable::class
            ])
            ->setForeignKey('page_id')
        ;
        $this->belongsTo('Content', [
            'className' => \Domain\Content\ContentsTable::class
            ])
            ->setForeignKey('content_id')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('page_id', ['type' => 'integer'])
            ->addColumn('content_id', ['type' => 'integer'])
            ->addConstraint(
                'primary',
                [
                    'type' => 'primary',
                    'columns' => [
                        'page_id',
                        'content_id'
                    ]
                ]
        );
    }
}
