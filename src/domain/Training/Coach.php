<?php

namespace Domain\Training;

class Coach extends \Cake\ORM\Entity
{
    use \Domain\DatetimeMetaTrait;

    protected $_hidden = ['user_id', 'user', 'member_id', 'member'];
}
