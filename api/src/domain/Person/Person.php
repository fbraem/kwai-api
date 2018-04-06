<?php

namespace Domain\Person;

class Person extends \Cake\ORM\Entity
{
    protected $_virtual = [
        'age'
    ];

    use \Domain\DatetimeMetaTrait;

    protected function _getBirthdate($value)
    {
        if ($value) {
            return (new \Carbon\Carbon($value))->toDateString();
        }
        return null;
    }

    // Returns the age of the person at the end of the year
    protected function _getAge()
    {
        $endOfYear = \Carbon\Carbon::now()->endOfYear();
        $birthDate = new \Carbon\Carbon($this->_properties['birthdate']);
        return $birthDate->diffInYears($endOfYear);
    }
}
