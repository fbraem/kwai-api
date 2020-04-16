<?php

use Phinx\Migration\AbstractMigration;

/**
 * Add news rules
 */
class NewsMergeContentMigration extends AbstractMigration
{
    const SUBJECT_NAME = 'news';

    public function up()
    {
        $this->table('news_stories')
            ->renameColumn('featured', 'promoted')
            ->renameColumn('featured_end_date', 'promoted_end_date')
            ->save()
        ;
        $this->table('news_contents_2', ['id' => false, 'primary_key' => ['news_id', 'locale']])
            ->addColumn('news_id', 'integer')
            ->addColumn('locale', 'string')
            ->addColumn('format', 'string')
            ->addColumn('title', 'string')
            ->addColumn('content', 'text', [ 'null' => true ])
            ->addColumn('summary', 'text')
            ->addColumn('user_id', 'integer')
            ->addTimestamps()
            ->create()
        ;

        $contentsQuery = $this->getQueryBuilder();
        $contentsQuery
            ->select([
                'news_id',
                'locale',
                'format',
                'title',
                'content',
                'summary',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->from('news_contents')
            ->join([
                'c' => [
                    'table' => 'contents',
                    'conditions' => [
                        'c.id = news_contents.content_id'
                    ]
                ]
            ])
        ;
        $builder = $this->getQueryBuilder();
        $builder
            ->insert([
                'news_id',
                'locale',
                'format',
                'title',
                'content',
                'summary',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->into('news_contents_2')
            ->values($contentsQuery)
            ->execute()
        ;
    }

    public function down()
    {
        $this->table('news_stories')
            ->renameColumn('promoted', 'featured')
            ->renameColumn('promoted_end_date', 'featured_end_date')
            ->save()
        ;

        $this->table('news_contents_2')
            ->drop()
            ->save()
        ;
    }
}
