<?php

namespace Domain\Training;

class Definition extends \Cake\ORM\Entity
{
    use \Domain\DatetimeMetaTrait;

    protected $_hidden = ['season_id', 'season', 'user_id', 'user', 'training_events'];
}
