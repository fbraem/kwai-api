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

    public function whereUser($userId)
    {
        $this->select->where(['user_id' => $id]);
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
