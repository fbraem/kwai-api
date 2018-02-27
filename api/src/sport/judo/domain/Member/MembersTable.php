<?php

namespace Judo\Domain\Member;

use \Zend\Db\Sql\Expression;
use \Zend\Db\TableGateway\TableGateway;

class MembersTable implements MembersInterface
{
    private $db;

    private $table;

    private $select;

    public function __construct($db)
    {
        $this->db = $db;

        $this->table = new TableGateway('sport_judo_members', $this->db);
        $this->select = $this->createSelect();
    }

    private function createSelect()
    {
        $select = $this->table->getSql()->select();

        $select->columns([
            'member_id' => 'id',
            'member_license' => 'license',
            'member_person_id' => 'person_id',
            'member_remark' => 'remark',
            'member_competition' => 'competition',
            'member_created_at' => 'created_at',
            'member_updated_at' => 'updated_at'
        ]);

        $select->join(
            'persons',
            'sport_judo_members.person_id = persons.id',
            [
                'person_firstname' => 'firstname',
                'person_lastname' => 'lastname'
            ],
            $select::JOIN_LEFT
        );

        return $select;
    }

    public function whereId($id)
    {
        $this->select->where(['sport_judo_members.id' => $id]);
        return $this;
    }

    public function whereLicense($license)
    {
        $this->select->where(['sport_judo_members.license' => $license]);
        return $this;
    }

    public function orderByName()
    {
        $this->select->order('sport_judo_persons.lastname ASC');
        $this->select->order('sport_judo_persons.firstname ASC');
        return $this;
    }

    public function findOne() : MemberInterface
    {
        $members = $this->find();
        if ($members && count($members) > 0) {
            return reset($members);
        }
        throw new \Domain\NotFoundException("Member not found");
    }

    public function find(?int $limit = null, ?int $offset = null) : iterable
    {
        if ($limit) {
            $this->select->limit($limit);
        }
        if ($offset) {
            $this->select->offset($offset);
        }

        $members = [];
        $persons = [];
        $grades = [];

        $result = $this->table->selectWith($this->select);
        if ($result->count() > 0) {
            foreach ($result as $row) {
                $member = self::getPrefixedValues($row, 'member_');
                $persons[$member['person_id']] = 1;
                $members[$member['id']] = $member;
            }
        }

        if (count($persons) > 0) {
            $persons = (new \Domain\Person\PersonsTable($this->db))->whereId(array_keys($persons))->find();
        }

        $result = [];
        foreach ($members as $member) {
            $member['person'] = $persons[$member['person_id']] ?? null;
            $result[] = new Member($this->db, $member);
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

    private static function getPrefixedValues($arr, $prefix)
    {
        $length = strlen($prefix);
        $result = array_filter(
            (array) $arr,
            function ($val, $key) use ($length, $prefix) {
                return substr($key, 0, $length) == $prefix;
            },
            ARRAY_FILTER_USE_BOTH
        );
        foreach ($result as $key => $value) {
            $result[substr($key, $length)] = $value;
            unset($result[$key]);
        }
        return $result;
    }
}
