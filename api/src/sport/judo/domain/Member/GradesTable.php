<?php

namespace Judo\Domain\Member;

use \Zend\Db\Sql\Expression;
use \Zend\Db\TableGateway\TableGateway;

class GradesTable implements GradesInterface
{
    private $db;

    private $table;

    private $select;

    public function __construct($db)
    {
        $this->db = $db;

        $this->table = new TableGateway('sport_judo_grades', $this->db);
        $this->select = $this->createSelect();
    }

    private function createSelect()
    {
        $select = $this->table->getSql()->select();
        $select->columns([
            'id',
            'grade',
            'name',
            'color',
            'position',
            'min_age',
            'prepare_time'
        ]);

        return $select;
    }

    public function whereId($id)
    {
        $this->select->where(['grades.id' => $id]);
        return $this;
    }

    public function findOne() : GradeInterface
    {
        $result = $this->table->selectWith($this->select);
        if ($result->count() > 0) {
            return new Grade($this->db, $result->current());
        }
        throw new \Domain\NotFoundException("Grade not found");
    }

    public function find(?int $limit = null, ?int $offset = null) : iterable
    {
        $this->select->order('position ASC');

        if ($limit) {
            $this->select->limit($limit);
        }
        if ($offset) {
            $this->select->offset($offset);
        }

        $grades = [];

        $result = $this->table->selectWith($this->select);
        if ($result->count() > 0) {
            foreach ($result as $row) {
                $grades[$row['id']] = new Grade($this->db, $row);
            }
        }
        return $grades;
    }

    public function count() : int
    {
        $select = clone $this->select;
        $select->columns(['c' => new Expression('COUNT(0)')]);
        $resultSet = $this->table->selectWith($select);
        return (int) $resultSet->current()->c;
    }
}
