<?php

namespace REST\Teams;

use Zend\Validator\Date;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;
use Zend\Validator\Digits;

class TeamTypeValidator implements \Core\ValidatorInterface
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

        $startAgeValidation = new Digits();
        $startAgeValidation->setMessage(
            _('start age must be a number'),
            Digits::NOT_DIGITS
        );
        $this->validator->addValidator('data.attributes.start_age', $startAgeValidation);

        $endAgeValidation = new Digits();
        $endAgeValidation->setMessage(
            _('End age must be a number'),
            Digits::NOT_DIGITS
        );
        $this->validator->addValidator('data.attributes.end_age', $endAgeValidation);

        return $this->validator->validate($data);
    }
}
