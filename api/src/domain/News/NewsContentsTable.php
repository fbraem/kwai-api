<?php

namespace Domain\News;

class NewsContentsTable
{
    private $db;

    private $select;

    public function __construct($db)
    {
        $this->db = $db;
        $this->table = new \Zend\Db\TableGateway\TableGateway('news_contents', $this->db);
        $this->select = $this->table->getSql()->select();
    }

    public function forNewsId($id)
    {
        $this->select->where(['news_id' => $id]);
        return $this;
    }

    public function find()
    {
        $this->select->columns([
            'content_id',
            'news_id'
        ]);
        $this->select
            ->join(
                'contents',
                'news_contents.content_id = contents.id',
                [
                    'content_id' => 'id',
                    'content_locale' => 'locale',
                    'content_format' => 'format',
                    'content_title' => 'title',
                    'content_content' => 'content',
                    'content_summary' => 'summary',
                    'content_user_id' => 'user_id',
                    'content_created_at' => 'created_at',
                    'content_updated_at' => 'updated_at'
                ]
            )
        ;

        $contents = [];

        $resultSet = $this->table->selectWith($this->select);
        foreach ($resultSet as $row) {
            if (! isset($contents[$row->news_id])) {
                $contents[$row->news_id] = new NewsContent($this->db, $row->news_id);
            }
            $contentData = array_filter(
                (array) $row,
                function ($val, $key) {
                    return substr($key, 0, 8) == 'content_';
                },
                ARRAY_FILTER_USE_BOTH
            );
            foreach ($contentData as $key => $value) {
                $contentData[substr($key, 8)] = $value;
                unset($contentData[$key]);
            }
            $contentData['id'] = $row->content_id;
            $contents[$row->news_id]->add(new \Domain\Content\Content($this->db, $contentData));
        }

        return $contents;
    }
}
