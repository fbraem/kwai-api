<?php

namespace Domain\Person;

class Contact implements ContactInterface
{
    private $db;

    private $id;
    private $email;
    private $tel;
    private $mobile;
    private $address;
    private $postal_code;
    private $city;
    private $county;
    private $country;
    private $remark;

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
        $this->email = $data['email'] ?? null;
        $this->tel = $data['tel'] ?? null;
        $this->mobile = $data['mobile'] ?? null;
        $this->address = $data['address'] ?? null;
        $this->postal_code = $data['postal_code'] ?? null;
        $this->city = $data['city'] ?? null;
        $this->county = $data['county'] ?? null;
        $this->country = $data['country'] ?? null;
        $this->remark = $data['remark'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    public function extract() : iterable
    {
        return [
            'id' => $this->id(),
            'email' => $this->email(),
            'tel' => $this->tel(),
            'mobile' => $this->mobile(),
            'address' => $this->address(),
            'postal_code' => $this->postal_code(),
            'city' => $this->city(),
            'county' => $this->county(),
            'remark' => $this->remark(),
            'created_at' => $this->createdAt(),
            'updated_at' => $this->updatedAt()
        ];
    }

    public function id() : ?int
    {
        return $this->id;
    }

    public function email() : ?string
    {
        return $this->email;
    }

    public function tel() : ?string
    {
        return $this->tel;
    }

    public function mobile() : ?string
    {
        return $this->mobile;
    }

    public function address() : ?string
    {
        return $this->address;
    }

    public function postal_code() : ?string
    {
        return $this->postal_code;
    }

    public function city() : ?string
    {
        return $this->city;
    }

    public function county() : ?string
    {
        return $this->county;
    }

    public function country() : ?\Domain\Person\CountryInterface
    {
        return $this->country;
    }

    public function remark() : ?string
    {
        return $this->remark;
    }

    public function store()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('contacts', $this->db);

        if ($this->id()) {
            $this->updated_at = \Carbon\Carbon::now()->toDateTimeString();
        } else {
            $this->created_at = \Carbon\Carbon::now()->toDateTimeString();
        }

        $data = $this->extract();
        if ($this->country) {
            $data['country_id'] = $this->country->id();
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
        $table = new \Zend\Db\TableGateway\TableGateway('contacts', $this->db);
        $table->delete(['id' => $this->id()]);
    }
}
