<?php

namespace Domain\Category;

class CategoriesTable implements CategoriesInterface
{
    private $db;

    private $table;

    private $select;

    public function __construct($db)
    {
        $this->db = $db;
        $this->table = new \Zend\Db\TableGateway\TableGateway('categories', $this->db);
        $this->select = $this->table->getSql()->select();
    }

    public function whereId($ids)
    {
        $this->select->where(['id' => $ids]);
        return $this;
    }

    public function findOne() : CategoryInterface
    {
        $categories = $this->find();
        if ($categories && count($categories) > 0) {
            return reset($categories);
        }
        throw new \Domain\NotFoundException("Category not found");
    }

    public function find() : iterable
    {
        $this->select->columns([
            'id',
            'name',
            'description',
            'remark',
            'user_id',
            'created_at',
            'updated_at'
        ]);

        $categories = [];
        $results = $this->table->selectWith($this->select);
        foreach ($results as $result) {
            $categories[$result->id] = new Category($this->db, $result);
        }
        return $categories;
    }

    public function count() : int
    {
        $this->select->columns(['c' => new \Zend\Db\Sql\Expression('COUNT(id)')]);
        $resultSet = $this->table->selectWith($this->select);
        return (int) $resultSet->current()->c;
    }
}
