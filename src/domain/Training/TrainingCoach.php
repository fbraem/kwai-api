<?php

namespace Domain\Training;

class TrainingCoach extends \Cake\ORM\Entity
{
    use \Domain\DatetimeMetaTrait;

    protected $_hidden = [
        'user',
        'user_id',
        'coach_id',
        'training_id'
    ];
}
