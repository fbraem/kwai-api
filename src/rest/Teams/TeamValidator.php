<?php

namespace REST\Teams;

use Zend\Validator\StringLength;

class TeamValidator extends \Core\Validators\InputValidator
{
    public function __construct()
    {
        parent::__construct();

        $nameValidation = new StringLength(['max' => 255]);
        $nameValidation->setMessage(
            _('name can\'t contain more then 255 characters'),
            StringLength::TOO_LONG
        );
        $this->addValidator('data.attributes.name', $nameValidation);
    }
}
