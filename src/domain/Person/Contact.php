<?php

namespace Domain\Person;

class Contact extends \Cake\ORM\Entity
{
    use \Domain\DatetimeMetaTrait;

    protected $_hidden = [ 'country_id' ];
}
