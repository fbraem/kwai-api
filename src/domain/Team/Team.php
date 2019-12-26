<?php

namespace Domain\Team;

class Team extends \Cake\ORM\Entity
{
    use \Domain\DatetimeMetaTrait;

    protected $_hidden = ['season_id', 'season', 'team_category', 'team_category_id' , 'members'];
}
