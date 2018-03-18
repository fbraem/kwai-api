<?php

namespace Domain\Person;

class Person extends \Cake\ORM\Entity
{
    use \Domain\DatetimeMetaTrait;

    public function _getBirthdate($value)
    {
        if ($value) {
            return (new \Carbon\Carbon($value))->toDateString();
        }
        return null;
    }
}
