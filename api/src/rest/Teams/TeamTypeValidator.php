<?php

namespace REST\Teams;

use Zend\Validator\Date;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;
use Zend\Validator\Digits;
use Zend\Validator\InArray;

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

        $genderValidation = new InArray([
            'haystack' => [ 0, 1, 2],
            'strict' => InArray::COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY
        ]);
        $genderValidation->setMessage(
            _('Not a valid gender'),
            InArray::NOT_IN_ARRAY
        );
        $this->validator->addValidator('data.attributes.gender', $genderValidation);

        return $this->validator->validate($data);
    }
}
