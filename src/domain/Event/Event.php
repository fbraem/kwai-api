<?php

namespace Domain\Event;

class Event extends \Cake\ORM\Entity
{
    use \Domain\DatetimeMetaTrait;

    protected $_hidden = [ 'category', 'category_id', 'user', 'user_id' ];

    public function _getStartDate($value)
    {
        if ($value) {
            $date = new \Carbon\Carbon($value, $this->time_zone);
            return $date->toDateTimeString();
        }
        return null;
    }

    public function _getEndDate($value)
    {
        if ($value) {
            $date = new \Carbon\Carbon($value, $this->time_zone);
            return $date->toDateTimeString();
        }
        return null;
    }
}
