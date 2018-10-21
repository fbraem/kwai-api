<?php

namespace REST\Teams;

class TeamTypeEmptyValidator extends \Core\Validators\EmptyValidator
{
    public function __construct()
    {
        parent::__construct();

        $this->addEmptyValidator('data.attributes.competition');
        $this->addEmptyValidator('data.attributes.gender');
    }
}
