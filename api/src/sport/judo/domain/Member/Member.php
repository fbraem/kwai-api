<?php

namespace Judo\Domain\Member;

class Member implements MemberInterface
{
    private $db;

    private $id;
    private $license;
    private $license_end_date;
    private $remark;
    private $competition;
    private $person;
    private $grades;

    use \Domain\DatetimeMetaTrait;

    public function __construct($db, ?iterable $data = null)
    {
        $this->db = $db;

        if ($data) {
            $this->hydrate($data);
        }
    }

    public function hydrate(iterable $data)
    {
        $this->id = $data['id'] ?? null;
        $this->license = $data['license'] ?? null;
        $this->license_end_date = $data['license_end_date'] ?? null;
        $this->remark = $data['remark'] ?? null;
        $this->competition = $data['competition'] ?? null;
        $this->person = $data['person'] ?? null;
        $this->grades = $data['grades'] ?? [];
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    public function extract() : iterable
    {
        return [
            'id' => $this->id(),
            'license' => $this->license,
            'license_end_date' => $this->license_end_date,
            'remark' => $this->remark,
            'competition' => $this->competition,
            'created_at' => $this->createdAt(),
            'updated_at' => $this->updatedAt()
        ];
    }

    public function id() : ?int
    {
        return $this->id;
    }

    /*
        public function license() : ?string
        {
            return $this->license;
        }

        public function remark() : ?string
        {
            return $this->remark;
        }

        public function competition() : ?boolean
        {
            return $this->competition;
        }
    */
    public function person() : ?\Domain\Person\PersonInterface
    {
        return $this->person;
    }

    public function grades() : ?iterable
    {
        return $this->grades;
    }

    public function store()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('sport_judo_members', $this->db);

        if ($this->id()) {
            $this->updated_at = \Carbon\Carbon::now()->toDateTimeString();
        } else {
            $this->created_at = \Carbon\Carbon::now()->toDateTimeString();
        }

        $data = $this->extract();
        if ($this->person) {
            $data['person_id'] = $this->person->id();
        }

        if ($this->id()) {
            $table->update($data, [
                'id' => $this->id()
            ]);
        } else {
            $table->insert($data);
            $this->id = $table->getLastInsertValue();
        }
    }

    public function delete()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('sport_judo_members', $this->db);
        $table->delete(['id' => $this->id()]);
    }
}
