<?php

namespace Domain\Content;

class ContentsTable implements ContentsInterface
{
    private $db;

    private $table;

    private $select;

    public function __construct($db)
    {
        $this->db = $db;
        $this->table = new \Zend\Db\TableGateway\TableGateway('contents', $this->db);
        $this->select = $this->table->getSql()->select();
    }

    public function forNewsStory($id)
    {
        return $this->forNewsStories([$id]);
    }

    public function forNewsStories(array $ids)
    {
        $this->select
            ->join(
                'contentables',
                'contents.id = contentables.content_id'
                )
            ->where([
                'contentables.contentable_id' => $ids,
                'contentables.contentable_type' => 'news'
            ]);
        return $this;
    }

    public function find() : iterable
    {
        $contents = $this->table->selectWith($this->select);

        if (count($contents) > 0) {
            $ids = array_unique(
                array_map(function ($v) {
                    return $v->user_id;
                }, $contents)
            );
            $users = (new \Domain\User\UsersTable($this->db))->whereId($ids)->find();
            foreach ($contents as $content) {
                $content->user = $users[$content->user_id];
            }
        }

        $result = [];
        foreach ($contents as $content) {
            $result[] = new Content($this->db, $content);
        }
        return $result;
    }

    public function count() : int
    {
        $this->select->columns(['c' => new \Zend\Db\Sql\Expression('COUNT(id)')]);
        $resultSet = $this->table->selectWith($this->select);
        return (int) $resultSet->current()->c;
    }
}
