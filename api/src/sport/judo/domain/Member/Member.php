<?php

namespace Judo\Domain\Member;

class Member extends \Cake\ORM\Entity
{
    use \Domain\DatetimeMetaTrait;

    public function _getLicenseEndDate($value)
    {
        if ($value) {
            return (new \Carbon\Carbon($value))->toDateString();
        }
        return null;
    }
}
