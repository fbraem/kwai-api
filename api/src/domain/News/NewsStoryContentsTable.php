<?php

namespace Domain\News;

class NewsStoryContentsTable extends \Cake\ORM\Table
{
    public static $registryName = 'NewsStoryContents';
    public static $tableName = 'news_contents';
    public static $entityClass = 'Domain\News\NewsStoryContent';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable(false);

        $this->belongsTo('NewsStory', [
            'className' => NewsStoriesTable::class
            ])
            ->setForeignKey('news_id')
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
            ->addColumn('news_id', ['type' => 'integer'])
            ->addColumn('content_id', ['type' => 'integer'])
            ->addConstraint(
                'primary',
                [
                    'type' => 'primary',
                    'columns' => [
                        'news_id',
                        'content_id'
                    ]
                ]
        );
    }
}
