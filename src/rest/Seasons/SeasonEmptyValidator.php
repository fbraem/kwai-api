<?php

namespace REST\Seasons;

class SeasonEmptyValidator extends \Core\Validators\EmptyValidator
{
    public function __construct()
    {
        parent::__construct();

        $this->addEmptyValidator('data.attributes.name');
        $this->addEmptyValidator('data.attributes.start_date', \Zend\Validator\NotEmpty::INTEGER | \Zend\Validator\NotEmpty::NULL);
        $this->addEmptyValidator('data.attributes.end_date', \Zend\Validator\NotEmpty::INTEGER | \Zend\Validator\NotEmpty::NULL);
    }
}
