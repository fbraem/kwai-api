<?php

namespace Domain\Page;

class PageContentsTable
{
    private $db;

    private $select;

    public function __construct($db)
    {
        $this->db = $db;
        $this->table = new \Zend\Db\TableGateway\TableGateway('page_contents', $this->db);
        $this->select = $this->table->getSql()->select();
    }

    public function forPageId($id)
    {
        $this->select->where(['page_id' => $id]);
        return $this;
    }

    public function find()
    {
        $this->select->columns([
            'content_id',
            'page_id'
        ]);
        $this->select
            ->join(
                'contents',
                'page_contents.content_id = contents.id',
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

        $selectString = $this->table->getSql()->buildSqlString($this->select);

        $contents = [];

        $resultSet = $this->table->selectWith($this->select);
        foreach ($resultSet as $row) {
            if (! isset($contents[$row->page_id])) {
                $contents[$row->page_id] = [];
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

            $contents[$row->page_id][$contentData['id']] = $contentData;
            $userIds[] = $contentData['user_id'];
        }

        $userIds = array_unique($userIds);
        $users = (new \Domain\User\UsersTable($this->db))->whereId($userIds)->find();

        foreach ($contents as $pageId => $pageContent) {
            foreach ($pageContent as $contentId => $content) {
                $contents[$pageId][$contentId]['user'] = $users[$content['user_id']];
            }
        }

        $result = [];
        foreach ($contents as $pageId => $pageContent) {
            $result[$pageId] = new PageContent($this->db, $pageId);
            foreach ($pageContent as $content) {
                $result[$pageId]->add(new \Domain\Content\Content($this->db, $content));
            }
        }

        return $result;
    }
}
