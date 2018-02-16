<?php

namespace Domain\Person;

use \Zend\Db\Sql\Expression;
use \Zend\Db\TableGateway\TableGateway;

class CountriesTable implements CountriesInterface
{
    private $db;

    private $table;

    private $select;

    public function __construct($db)
    {
        $this->db = $db;

        $this->table = new TableGateway('countries', $this->db);
        $this->select = $this->createSelect();
    }

    private function createSelect()
    {
        $select = $this->table->getSql()->select();
        $select->columns([
            'id',
            'iso_2',
            'iso_3',
            'name',
            'created_at',
            'updated_at'
        ]);

        return $select;
    }

    public function whereId($id)
    {
        $this->select->where(['pages.id' => $id]);
        return $this;
    }

    public function whereUser($id)
    {
        $this->select->where(['contents.user_id' => $id]);
    }

    public function orderByIso2()
    {
        $this->select->order('iso_2 ASC');
        return $this;
    }

    public function orderByIso3()
    {
        $this->select->order('iso_3 ASC');
        return $this;
    }

    public function orderByName()
    {
        $this->select->order('name ASC');
        return $this;
    }

    public function findOne() : CountryInterface
    {
        $result = $this->table->selectWith($this->select);
        if ($result->count() > 0) {
            return new Country($this->db, $result->current());
        }
        throw new \Domain\NotFoundException("Country not found");
    }

    public function find(?int $limit = null, ?int $offset = null) : iterable
    {
        if ($limit) {
            $this->select->limit($limit);
        }
        if ($offset) {
            $this->select->offset($offset);
        }

        $countries = [];

        $result = $this->table->selectWith($this->select);
        if ($result->count() > 0) {
            foreach ($result as $row) {
                $countries[$row['id']] = new Country($this->db, $row);
            }
        }
        return $countries;
    }

    public function count() : int
    {
        $select = clone $this->select;
        $select->columns(['c' => new Expression('COUNT(0)')]);
        $resultSet = $this->table->selectWith($select);
        return (int) $resultSet->current()->c;
    }
}
