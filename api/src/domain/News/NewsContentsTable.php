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
        $userIds = [];

        $resultSet = $this->table->selectWith($this->select);
        foreach ($resultSet as $row) {
            if (! isset($contents[$row->news_id])) {
                $contents[$row->news_id] = [];
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

            $contents[$row->news_id][$contentData['id']] = $contentData;
            $userIds[] = $contentData['user_id'];
        }

        $userIds = array_unique($userIds);
        $users = (new \Domain\User\UsersTable($this->db))->whereId($userIds)->find();

        foreach ($contents as $newsId => $newsContent) {
            foreach ($newsContent as $contentId => $content) {
                $contents[$newsId][$contentId]['user'] = $users[$content['user_id']];
            }
        }

        $result = [];
        foreach ($contents as $newsId => $newsContent) {
            $result[$newsId] = new NewsContent($this->db, $newsId);
            foreach ($newsContent as $content) {
                $result[$newsId]->add(new \Domain\Content\Content($this->db, $content));
            }
        }

        return $result;
    }
}
