<?php

namespace Domain\Page;

use \Zend\Db\Sql\Expression;
use \Zend\Db\TableGateway\TableGateway;

class PagesTable implements PagesInterface
{
    private $db;

    private $table;

    private $select;

    public function __construct($db)
    {
        $this->db = $db;

        $this->table = new TableGateway('pages', $this->db);
        $this->select = $this->table->getSql()->select();
    }

    public function whereCategory($id)
    {
        $this->select->where(['category_id' => $id]);
        return $this;
    }

    public function whereAllowedToSee()
    {
        $this->select->where
            ->equalTo('enabled', 1);
    }

    public function whereId($id)
    {
        $this->select->where(['id' => $id]);
        return $this;
    }

    public function orderByDate()
    {
        $this->select->order('created_at DESC');
        return $this;
    }

    public function findOne() : PageInterface
    {
        $result = $this->find();
        if ($result && count($result) > 0) {
            return reset($result);
        }
        throw new \Domain\NotFoundException("Page not found");
    }

    public function find(?int $limit = null, ?int $offset = null) : iterable
    {
        $this->select->columns([
            'id',
            'enabled',
            'remark',
            'category_id',
            'created_at',
            'updated_at'
        ]);

        if ($limit) {
            $this->select->limit($limit);
        }
        if ($offset) {
            $this->select->offset($offset);
        }

        $pages = [];
        $resultSet = $this->table->selectWith($this->select);
        foreach ($resultSet as $row) {
            $pages[$row->id] = $row;
        }

        if (count($pages) > 0) {
            $contents = (new PageContentsTable($this->db))->forPageId(array_keys($pages))->find();
            foreach ($contents as $pageId => $content) {
                $page = $pages[$pageId];
                $page->contents = $content;
            }

            $ids = array_unique(
                array_map(function ($v) {
                    return $v->category_id;
                }, $pages)
            );
            $categories = (new \Domain\Category\CategoriesTable($this->db))->whereId($ids)->find();
            foreach ($pages as $page) {
                $page->category = $categories[$page->category_id];
            }
        }

        $result = [];
        foreach ($pages as $page) {
            $result[] = new Page($this->db, $page);
        }
        return $result;
    }

    public function count() : int
    {
        $this->select->columns(['c' => new Expression('COUNT(id)')]);
        $resultSet = $this->table->selectWith($this->select);
        return (int) $resultSet->current()->c;
    }
}
