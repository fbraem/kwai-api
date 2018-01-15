<?php

namespace Domain\User;

class UsersTable implements UsersInterface
{
    private $db;

    private $table;

    private $select;

    public function __construct($db)
    {
        $this->db = $db;
        $this->table = new \Zend\Db\TableGateway\TableGateway('users', $this->db);
        $this->select = $this->table->getSql()->select();
    }

    public function whereIds($ids)
    {
        $this->select->where(['id' => $ids]);
        return $this;
    }

    public function whereId($id)
    {
        $this->select->where(['id' => $id]);
        return $this;
    }

    public function whereEmail($email)
    {
        $this->select->where(['email' => $email]);
        return $this;
    }

    public function findOne() : UserInterface
    {
        $users = $this->find();
        if ($users && count($users) > 0) {
            return reset($users);
        }
        throw new \Domain\NotFoundException("User not found");
    }

    public function find() : iterable
    {
        $this->select->columns([
            'id',
            'email',
            'password',
            'last_login',
            'first_name',
            'last_name',
            'remark',
            'created_at',
            'updated_at'
        ]);

        $users = [];
        $results = $this->table->selectWith($this->select);
        foreach ($results as $result) {
            $users[$result->id] = new User($this->db, $result);
        }
        return $users;
    }

    public function count() : int
    {
        $this->select->columns(['c' => new \Zend\Db\Sql\Expression('COUNT(id)')]);
        $resultSet = $this->table->selectWith($this->select);
        return (int) $resultSet->current()->c;
    }
}
