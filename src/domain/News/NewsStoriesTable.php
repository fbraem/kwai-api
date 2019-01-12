<?php

namespace Domain\News;

class NewsStoriesTable extends \Cake\ORM\Table
{
    public static $registryName = 'NewsStories';
    public static $tableName = 'news_stories';
    public static $entityClass = 'Domain\News\NewsStory';

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
                'joinTable' => 'news_contents',
                'through' => NewsStoryContentsTable::getTableFromRegistry(),
                'dependent' => true
            ])
            ->setForeignKey('news_id')
            ->setProperty('contents')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('category_id', [ 'type' => 'integer' ])
            ->addColumn('enabled', [ 'type' => 'boolean' ])
            ->addColumn('featured', [ 'type' => 'integer' ])
            ->addColumn('featured_end_date', [ 'type' => 'datetime' ])
            ->addColumn('publish_date', [ 'type' => 'datetime' ])
            ->addColumn('timezone', [ 'type' => 'string' ])
            ->addColumn('end_date', [ 'type' => 'datetime' ])
            ->addColumn('remark', [ 'type' => 'text'])
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
