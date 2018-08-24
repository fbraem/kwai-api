<?php

namespace Domain\Category;

class Category extends \Cake\ORM\Entity
{
    protected $_hidden = [ 'user', 'user_id' ];

    use \Domain\DatetimeMetaTrait;
}
