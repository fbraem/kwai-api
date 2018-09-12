<?php
namespace Domain\User;

class UserInvitation extends \Cake\ORM\Entity
{
    protected $_hidden = ['user', 'user_id'];

    use \Domain\DatetimeMetaTrait;

    public function _getExpiredAt($value)
    {
        if ($value) {
            $date = new \Carbon\Carbon($value, 'UTC');
            return $date->toDateTimeString();
        }
        return null;
    }
}
