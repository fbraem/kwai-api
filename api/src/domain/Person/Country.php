<?php

namespace Domain\Person;

class Country implements CountryInterface
{
    private $db;

    private $id;
    private $iso_2;
    private $iso_3;
    private $name;

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
        $this->iso_2 = $data['iso_2'] ?? null;
        $this->iso_3 = $data['iso_3'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    public function extract() : iterable
    {
        return [
            'id' => $this->id(),
            'iso_2' => $this->iso_2(),
            'iso_3' => $this->iso_3(),
            'name' => $this->name(),
            'created_at' => $this->createdAt(),
            'updated_at' => $this->updatedAt()
        ];
    }

    public function id() : ?int
    {
        return $this->id;
    }

    public function iso_2() : ?string
    {
        return $this->iso_2;
    }

    public function iso_3() : ?string
    {
        return $this->iso_3;
    }

    public function name() : ?string
    {
        return $this->name;
    }

    public function store()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('countries', $this->db);

        if ($this->id()) {
            $this->updated_at = \Carbon\Carbon::now()->toDateTimeString();
        } else {
            $this->created_at = \Carbon\Carbon::now()->toDateTimeString();
        }

        $data = $this->extract();

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
        $table = new \Zend\Db\TableGateway\TableGateway('countries', $this->db);
        $table->delete(['id' => $this->id()]);
    }
}
