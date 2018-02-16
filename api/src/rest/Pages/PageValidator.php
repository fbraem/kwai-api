<?php

namespace REST\Pages;

use Zend\Validator\Digits;

class PageValidator implements \Core\ValidatorInterface
{
    private $validator;

    public function __construct()
    {
        $this->validator = new \Core\Validator();
    }

    public function validate($data)
    {
        $priorityValidation = new Digits();
        $priorityValidation->setMessage(
            _('Priority must be a number'),
            Digits::NOT_DIGITS
        );
        $this->validator->addValidator('data.attributes.priority', $priorityValidation);

        return $this->validator->validate($data);
    }
}
