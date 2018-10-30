<?php

namespace Domain\Game;

class Season extends \Cake\ORM\Entity
{
    protected $_hidden = ['teams'];

    public function _getStartDate($value)
    {
        if ($value) {
            return (new \Carbon\Carbon($value))->toDateString();
        }
        return null;
    }

    public function _getEndDate($value)
    {
        if ($value) {
            return (new \Carbon\Carbon($value))->toDateString();
        }
        return null;
    }

    use \Domain\DatetimeMetaTrait;
}
