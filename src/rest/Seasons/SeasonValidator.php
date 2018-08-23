<?php

namespace REST\Seasons;

use Zend\Validator\Date;
use Zend\Validator\StringLength;

class SeasonValidator extends \Core\Validator
{
    public function __construct()
    {
        parent::__construct();
    }

    public function validate($data)
    {
        $nameValidation = new StringLength(['max' => 255]);
        $nameValidation->setMessage(
            _('name can\'t contain more then 255 characters'),
            StringLength::TOO_LONG
        );
        $this->addValidator('data.attributes.name', $nameValidation);

        $dateValidator = new Date(['format' => 'Y-m-d']);
        $dateValidator->setMessage(
            _('Invalid date'),
            Date::INVALID_DATE
        );
        $this->addValidator('data.attributes.start_date', $dateValidator);
        $this->addValidator('data.attributes.end_date', $dateValidator);

        return parent::validate($data);
    }
}
