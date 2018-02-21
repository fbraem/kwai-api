<?php

namespace Domain\Person;

use \Zend\Db\Sql\Expression;
use \Zend\Db\TableGateway\TableGateway;

class ContactsTable implements ContactsInterface
{
    private $db;

    private $table;

    private $select;

    public function __construct($db)
    {
        $this->db = $db;

        $this->table = new TableGateway('contacts', $this->db);
        $this->select = $this->createSelect();
    }

    private function createSelect()
    {
        $select = $this->table->getSql()->select();
        $select->columns([
            'id',
            'email',
            'tel',
            'mobile',
            'address',
            'postal_code',
            'city',
            'county',
            'country_id',
            'remark',
            'created_at',
            'updated_at'
        ]);
        return $select;
    }

    public function whereId($id)
    {
        $this->select->where(['id' => $id]);
        return $this;
    }

    public function findOne() : ContactInterface
    {
        $contacts = $this->find();
        if ($contacts && count($contacts) > 0) {
            return reset($contacts);
        }
        throw new \Domain\NotFoundException("Contact not found");
    }

    public function find(?int $limit = null, ?int $offset = null) : iterable
    {
        if ($limit) {
            $this->select->limit($limit);
        }
        if ($offset) {
            $this->select->offset($offset);
        }

        $result = [];
        $contacts = [];
        $countries = [];

        $result = $this->table->selectWith($this->select);
        if ($result->count() > 0) {
            foreach ($result as $row) {
                $countries[$row['country_id']] = 1;
                $contacts[$row['id']] = $row;
            }
        }

        if (count($countries) > 0) {
            $countries = (new CountriesTable($this->db))->whereId(array_keys($countries))->find();
        }

        $result = [];
        foreach ($contacts as $contact) {
            $contact['country'] = $countries[$contact['country_id']] ?? null;
            $result[] = new Contact($this->db, $contact);
        }

        return $result;
    }

    public function count() : int
    {
        $select = clone $this->select;
        $select->columns(['c' => new Expression('COUNT(0)')]);
        $resultSet = $this->table->selectWith($select);
        return (int) $resultSet->current()->c;
    }
}
