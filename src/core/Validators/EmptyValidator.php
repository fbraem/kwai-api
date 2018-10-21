<?php

namespace Core\Validators;

use Zend\Validator\NotEmpty;

class EmptyValidator extends InputValidator
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addEmptyValidator($path, $option = null)
    {
        $notEmpty = new NotEmpty($option);
        $notEmpty->setMessage(_("Value can't be empty"));
        parent::addValidator($path, $notEmpty);
    }
}
