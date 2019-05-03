<?php

namespace Domain\Training;

class Presence extends \Cake\ORM\Entity
{
    use \Domain\DatetimeMetaTrait;

    protected $_hidden = [
        'user_id',
        'user',
        'member_id',
        'member',
        'training_id',
        'training'
    ];
}
