<?php

namespace REST\News;

use Zend\Validator\Digits;
use Zend\Validator\Date;

class NewsStoryValidator extends \Core\Validators\InputValidator
{
    public function __construct()
    {
        parent::__construct();

        $featuredValidation = new Digits();
        $featuredValidation->setMessage(
            _('featured must be a number'),
            Digits::NOT_DIGITS
        );
        $this->addValidator('data.attributes.featured', $featuredValidation);

        $dateValidator = new Date(['format' => 'Y-m-d H:i:s']);
        $dateValidator->setMessage(
            _('Invalid date'),
            Date::INVALID_DATE
        );
        $this->addValidator('data.attributes.publish_date', $dateValidator);
        $this->addValidator('data.attributes.featured_end_date', $dateValidator);
        $this->addValidator('data.attributes.end_date', $dateValidator);
    }
}
