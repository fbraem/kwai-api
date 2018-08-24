<?php

namespace REST\Teams;

class TeamTypeEmptyValidator extends \Core\Validators\EmptyValidator
{
    public function __construct()
    {
        parent::__construct();

        $this->addValidator('data.attributes.competition');
        $this->addValidator('data.attributes.gender');
    }
}
