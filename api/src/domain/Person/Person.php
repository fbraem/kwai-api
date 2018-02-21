<?php

namespace Domain\Person;

class Person implements PersonInterface
{
    private $db;

    private $id;
    private $lastname;
    private $firstname;
    private $gender;
    private $active;
    private $birthdate;
    private $remark;
    private $user;
    private $contact;
    private $code;
    private $nationality;

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
        $this->lastname = $data['lastname'] ?? null;
        $this->firstname = $data['firstname'] ?? null;
        $this->gender = $data['gender'] ?? null;
        $this->active = $data['active'] ?? 0;
        $this->birthdate = $data['birthdate'] ?? null;
        $this->remark = $data['remark'] ?? null;
        $this->user = $data['user'] ?? null;
        $this->contact = $data['contact'] ?? null;
        $this->code = $data['code'] ?? null;
        $this->nationality = $data['nationality'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    public function extract() : iterable
    {
        return [
            'id' => $this->id(),
            'lastname' => $this->lastname(),
            'firstname' => $this->firstname(),
            'gender' => $this->gender(),
            'active' => $this->active(),
            'birthdate' => $this->birthdate(),
            'remark' => $this->remark(),
            'code' => $this->code(),
            'created_at' => $this->createdAt(),
            'updated_at' => $this->updatedAt()
        ];
    }

    public function id() : ?int
    {
        return $this->id;
    }

    public function lastname() : ?string
    {
        return $this->lastname;
    }

    public function firstname() : ?string
    {
        return $this->firstname;
    }

    public function gender() : ?int
    {
        return $this->gender;
    }

    public function active() : ?bool
    {
        return $this->active;
    }

    public function birthdate() : ?string
    {
        return $this->birthdate;
    }

    public function remark() : ?string
    {
        return $this->remark;
    }

    public function user() : ?\Domain\User\UserInterface
    {
        return $this->user;
    }

    public function contact() : ?\Domain\Person\ContactInterface
    {
        return $this->contact;
    }

    public function code() : ?string
    {
        return $this->code;
    }

    public function nationality() : ?\Domain\Person\CountryInterface
    {
        return $this->nationality;
    }

    public function store()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('persons', $this->db);

        if ($this->id()) {
            $this->updated_at = \Carbon\Carbon::now()->toDateTimeString();
        } else {
            $this->created_at = \Carbon\Carbon::now()->toDateTimeString();
        }

        $data = $this->extract();
        if ($this->user) {
            $data['user_id'] = $this->user->id();
        }
        if ($this->contact) {
            $data['contact_id'] = $this->contact->id();
        }
        if ($this->nationality) {
            $data['nationality_id'] = $this->nationality->id();
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
        $table = new \Zend\Db\TableGateway\TableGateway('persons', $this->db);
        $table->delete(['id' => $this->id()]);
    }
}
