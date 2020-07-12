<?php

use Phinx\Migration\AbstractMigration;

/**
 * Migration to move pages to a separate contents table.
 */
class PageMergeContentMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('page_contents_2', ['id' => false, 'primary_key' => ['page_id', 'locale']])
            ->addColumn('page_id', 'integer')
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
                'page_id',
                'locale',
                'format',
                'title',
                'content',
                'summary',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->from('page_contents')
            ->join([
                'c' => [
                    'table' => 'contents',
                    'conditions' => [
                        'c.id = page_contents.content_id'
                    ]
                ]
            ])
        ;
        $builder = $this->getQueryBuilder();
        $builder
            ->insert([
                'page_id',
                'locale',
                'format',
                'title',
                'content',
                'summary',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->into('page_contents_2')
            ->values($contentsQuery)
            ->execute()
        ;
    }

    public function down()
    {
        $this->table('page_contents_2')
            ->drop()
            ->save()
        ;
    }
}
