<?php

namespace Domain\Content;

class Content extends \Cake\ORM\Entity
{
    use \Domain\DatetimeMetaTrait;

    protected $_hidden = ['user', 'user_id'];
}
