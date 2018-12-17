<?php

namespace Domain\Training;

class Definition extends \Cake\ORM\Entity
{
    use \Domain\DatetimeMetaTrait;

    protected $_hidden = [
        'season_id',
        'season',
        'team_id',
        'team',
        'user_id',
        'user',
        'training_events'
    ];

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
