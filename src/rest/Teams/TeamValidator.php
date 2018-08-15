<?php

namespace REST\Teams;

use Zend\Validator\StringLength;

class TeamValidator implements \Core\ValidatorInterface
{
    private $validator;

    public function __construct()
    {
        $this->validator = new \Core\Validator();
    }

    public function validate($data)
    {
        $validators = [];

        $nameValidation = new StringLength(['max' => 255]);
        $nameValidation->setMessage(
            _('name can\'t contain more then 255 characters'),
            StringLength::TOO_LONG
        );
        $this->validator->addValidator('data.attributes.name', $nameValidation);

        return $this->validator->validate($data);
    }
}
