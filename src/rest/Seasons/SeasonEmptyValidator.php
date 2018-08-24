<?php

namespace REST\Seasons;

class SeasonEmptyValidator extends \Core\Validators\EmptyValidator
{
    public function __construct()
    {
        parent::__construct();

        $this->addValidator('data.attributes.name');
        $this->addValidator('data.attributes.start_date', \Zend\Validator\NotEmpty::INTEGER | \Zend\Validator\NotEmpty::NULL);
        $this->addValidator('data.attributes.end_date', \Zend\Validator\NotEmpty::INTEGER | \Zend\Validator\NotEmpty::NULL);
    }
}
