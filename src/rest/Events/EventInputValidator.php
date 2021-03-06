<?php

namespace REST\Events;

use Core\Validators\InputValidator;
use Respect\Validation\Validator as v;

class EventInputValidator extends InputValidator
{
    public function __construct($optional = false)
    {
        parent::__construct([
            'data.attributes.start_date' => v::date('Y-m-d H:i'),
            'data.attributes.end_date' => v::date('Y-m-d H:i'),
            'data.attributes.time_zone' => v::length(1, 255),
            'data.attributes.active' => [ v::boolType(), true ],
            'data.attributes.location' => [ v::length(1, 255), true ]
        ], $optional);
    }
}
