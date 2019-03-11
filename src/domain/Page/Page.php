<?php

namespace Domain\Page;

class Page extends \Cake\ORM\Entity
{
    use \Domain\DatetimeMetaTrait;

    protected $_hidden = [ 'category' ];
}
