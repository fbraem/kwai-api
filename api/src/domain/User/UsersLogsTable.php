<?php

namespace Domain\User;

class UserLogsTable implements UserLogsInterface
{
    private $db;

    private $table;

    private $select;

    public function __construct($db)
    {
        $this->db = $db;
        $this->table = new \Zend\Db\TableGateway\TableGateway('user_logs', $this->db);
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

    public function findOne() : UserLogInterface
    {
        $logs = $this->find();
        if ($logs && count($logs) > 0) {
            return reset($logs);
        }
        throw new \Domain\NotFoundException("Userlog not found");
    }

    public function find() : iterable
    {
        $this->select->columns([
            'id',
            'user_id',
            'action',
            'rest',
            'model_id',
            'created_at',
            'updated_at'
        ]);

        $logs = [];
        $results = $this->table->selectWith($this->select);
        foreach ($results as $result) {
            $logs[$result->id] = new UserLog($this->db, $result);
        }
        if (count($logs) > 0) {
            $ids = array_unique(
                array_map(function ($v) {
                    return $v->user_id;
                }, $logs)
            );
            $users = (new UsersTable($this->db))->whereId($ids)->find();
            foreach ($logs as $log) {
                $log->user = $users[$log->user_id];
            }
        }
        return $logs;
    }

    public function count() : int
    {
        $this->select->columns(['c' => new \Zend\Db\Sql\Expression('COUNT(id)')]);
        $resultSet = $this->table->selectWith($this->select);
        return (int) $resultSet->current()->c;
    }
}
