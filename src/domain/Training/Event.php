<?php

namespace Domain\Training;

class Event extends \Cake\ORM\Entity
{
    use \Domain\DatetimeMetaTrait;

    protected $_hidden = [ 'season', 'season_id', 'training_definition', 'training_definition_id', "user", "user_id" ];

    public function _getStartDate($value)
    {
        if ($value) {
            $date = new \Carbon\Carbon($value, 'UTC');
            return $date->toDateString();
        }
        return null;
    }

    public function _getStartTime($value)
    {
        if ($value) {
            $date = new \Carbon\Carbon($value, 'UTC');
            return $date->toTimeString();
        }
        return null;
    }

    public function _getEndTime($value)
    {
        if ($value) {
            $date = new \Carbon\Carbon($value, 'UTC');
            return $date->toTimeString();
        }
        return null;
    }
}
