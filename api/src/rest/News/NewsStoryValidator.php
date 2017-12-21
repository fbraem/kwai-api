<?php

namespace REST\News;

use Zend\Validator\Digits;
use Zend\Validator\Date;

class NewsStoryValidator implements \Core\ValidatorInterface
{
    private $validator;

    public function __construct()
    {
        $this->validator = new \Core\Validator();
    }

    public function validate($data)
    {
        $featuredValidation = new Digits();
        $featuredValidation->setMessage(
            _('featured must be a number'),
            Digits::NOT_DIGITS
        );
        $this->validator->addValidator('data.attributes.featured', $featuredValidation);

        $dateValidator = new Date(['format' => 'Y-m-d H:i:s']);
        $dateValidator->setMessage(
            _('Invalid date'),
            Date::INVALID_DATE
        );
        $this->validator->addValidator('data.attributes.publish_date', $dateValidator);
        $this->validator->addValidator('data.attributes.featured_end_date', $dateValidator);
        $this->validator->addValidator('data.attributes.end_date', $dateValidator);

        return $this->validator->validate($data);
    }
}
