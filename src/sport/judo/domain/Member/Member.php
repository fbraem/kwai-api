<?php

namespace Judo\Domain\Member;

class Member extends \Cake\ORM\Entity
{
    use \Domain\DatetimeMetaTrait;

    protected $_hidden = ['person'];

    public function _getLicenseEndDate($value)
    {
        if ($value) {
            return $value->toDateString();
        }
        return null;
    }
}
