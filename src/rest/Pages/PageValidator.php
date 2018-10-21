<?php

namespace REST\Pages;

use Zend\Validator\Digits;

class PageValidator extends \Core\Validators\InputValidator
{
    public function __construct()
    {
        parent::__construct();

        $priorityValidation = new Digits();
        $priorityValidation->setMessage(
            _('Priority must be a number'),
            Digits::NOT_DIGITS
        );
        $this->addValidator('data.attributes.priority', $priorityValidation);
    }
}
