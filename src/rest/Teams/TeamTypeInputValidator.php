<?php

namespace REST\Teams;

use Zend\Validator\Date;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;
use Zend\Validator\Digits;
use Zend\Validator\InArray;
use Zend\Validator\Callback;

class TeamTypeInputValidator extends \Core\Validators\InputValidator
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

        $boolValidation = new Callback(is_bool);
        $boolValidation->setMessage(
            _('Invalid value'),
            Callback::INVALID_VALUE
        );
        $this->addValidator('data.attributes.competition', $boolValidation);
        $this->addValidator('data.attributes.active', $boolValidation);

        $startAgeValidation = new Digits();
        $startAgeValidation->setMessage(
            _('start age must be a number'),
            Digits::NOT_DIGITS
        );
        $this->addValidator('data.attributes.start_age', $startAgeValidation);

        $endAgeValidation = new Digits();
        $endAgeValidation->setMessage(
            _('End age must be a number'),
            Digits::NOT_DIGITS
        );
        $this->addValidator('data.attributes.end_age', $endAgeValidation);

        $genderValidation = new InArray([
            'haystack' => [ 0, 1, 2],
            'strict' => InArray::COMPARE_NOT_STRICT_AND_PREVENT_STR_TO_INT_VULNERABILITY
        ]);
        $genderValidation->setMessage(
            _('Not a valid gender'),
            InArray::NOT_IN_ARRAY
        );
        $this->addValidator('data.attributes.gender', $genderValidation);
    }
}
