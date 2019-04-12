<?php

namespace Domain\Member;

class MemberImport extends \Cake\ORM\Entity
{
    use \Domain\DatetimeMetaTrait;

    protected $_hidden = [ 'user', 'user_id' ];
}
