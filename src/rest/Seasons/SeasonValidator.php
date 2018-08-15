<?php

namespace REST\Seasons;

use Zend\Validator\Date;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

class SeasonValidator implements \Core\ValidatorInterface
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

        $dateValidator = new Date(['format' => 'Y-m-d']);
        $dateValidator->setMessage(
            _('Invalid date'),
            Date::INVALID_DATE
        );
        $this->validator->addValidator('data.attributes.start_date', $dateValidator);
        $this->validator->addValidator('data.attributes.end_date', $dateValidator);

        return $this->validator->validate($data);
    }
}
