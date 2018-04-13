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
            return $value->toDateString();
        }
        return null;
    }

    // Returns the age of the person at the end of the year
    protected function _getAge()
    {
        $birthDate = $this->_properties['birthdate'];
        if ($birthDate) {
            return $birthDate->diffInYears(\Cake\I18n\Date::now()->endOfYear());
        }
        return -1;
    }
}
