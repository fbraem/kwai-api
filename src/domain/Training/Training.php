<?php

namespace Domain\Training;

class Training extends \Cake\ORM\Entity
{
    use \Domain\DatetimeMetaTrait;

    protected $_hidden = [
        'season',
        'season_id',
        'definition',
        'definition_id',
        'event_id',
        'coaches',
        'teams',
        'presences'
    ];
}
