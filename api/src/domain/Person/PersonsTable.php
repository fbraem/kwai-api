<?php

namespace Domain\Person;

use \Zend\Db\Sql\Expression;
use \Zend\Db\TableGateway\TableGateway;

class PersonsTable implements PersonsInterface
{
    private $db;

    private $table;

    private $select;

    public function __construct($db)
    {
        $this->db = $db;

        $this->table = new TableGateway('persons', $this->db);
        $this->select = $this->createSelect();
    }

    private function createSelect()
    {
        $select = $this->table->getSql()->select();

        $select->columns([
            'person_id' => 'id',
            'person_lastname' => 'lastname',
            'person_firstname' => 'firstname',
            'person_active' => 'active',
            'person_gender' => 'gender',
            'person_birthdate' => 'birthdate',
            'person_remark' => 'remark',
            'person_code' => 'code',
            'person_contact_id' => 'contact_id',
            'person_nationality_id' => 'nationality_id',
            'person_created_at' => 'created_at',
            'person_updated_at' => 'updated_at'
        ]);

        $select->join(
            'users',
            'persons.user_id = users.id',
            [
                'user_id' => 'id',
                'user_email' => 'email',
                'user_last_login' => 'last_login',
                'user_first_name' => 'first_name',
                'user_last_name' => 'last_name',
                'user_remark' => 'remark',
                'user_created_at' => 'created_at',
                'user_updated_at' => 'updated_at'
            ],
            $select::JOIN_LEFT
        );
        $select->join(
            'contacts',
            'persons.contact_id = contacts.id',
            [
                'contact_id' => 'id',
                'contact_email' => 'email',
                'contact_tel' => 'tel',
                'contact_mobile' => 'mobile',
                'contact_address' => 'address',
                'contact_postal_code' => 'postal_code',
                'contact_city' => 'city',
                'contact_county' => 'county',
                'contact_remark' => 'remark',
                'contact_country_id' => 'country_id',
                'contact_created_at' => 'created_at',
                'contact_updated_at' => 'updated_at'
            ],
            $select::JOIN_LEFT
        );

        return $select;
    }

    public function whereId($id)
    {
        $this->select->where(['persons.id' => $id]);
        return $this;
    }

    public function orderByName()
    {
        $this->select->order('persons.lastname ASC');
        $this->select->order('persons.firstname ASC');
        return $this;
    }

    public function findOne() : PersonInterface
    {
        $persons = $this->find();
        if ($persons && count($persons) > 0) {
            return reset($persons);
        }
        throw new \Domain\NotFoundException("Person not found");
    }

    public function find(?int $limit = null, ?int $offset = null) : iterable
    {
        if ($limit) {
            $this->select->limit($limit);
        }
        if ($offset) {
            $this->select->offset($offset);
        }

        $persons = [];
        $contacts = [];
        $countries = [];
        $users = [];

        $result = $this->table->selectWith($this->select);
        if ($result->count() > 0) {
            foreach ($result as $row) {
                $person = self::getPrefixedValues($row, 'person_');
                $user = self::getPrefixedValues($row, 'user_');
                if ($user['id']) {
                    $person['user'] = new \Domain\User\User($this->db, self::getPrefixedValues($row, 'user_'));
                }
                $contact = self::getPrefixedValues($row, 'contact_');
                if ($contact['id']) {
                    $contacts[$contact['id']] = $contact;
                    $countries[$contact['country_id']] = 1;
                }
                $countries[$person['nationality_id']] = 1;
                $persons[$person['id']] = $person;
            }
        }

        if (count($countries) > 0) {
            $countries = (new CountriesTable($this->db))->whereId(array_keys($countries))->find();
        }

        $result = [];
        foreach ($persons as $person) {
            $contact = $contacts[$person['contact_id']] ?? null;
            if ($contact) {
                if (isset($countries[$contact['country_id']])) {
                    $person['contact']['country'] = $countries[$contact['country_id']];
                }
                $person['contact'] = new Contact($this->db, $contact);
            }
            if (isset($countries[$person['nationality_id']])) {
                $person['nationality'] = $countries[$person['nationality_id']];
            }
            $result[] = new Person($this->db, $person);
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
