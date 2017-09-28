<?php

namespace Domain\News;

use Zend\Validator\Digits;
use Zend\Validator\StringLength;
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
        $titleValidation = new StringLength([
            'min' => 1,
            'max' => 255
        ]);
        $titleValidation->setMessages([
            StringLength::TOO_LONG => _('Title can\'t contain more then 255 characters'),
            StringLength::TOO_SHORT => _('Title can\'t be empty')
        ]);
        $this->validator->addValidator('data.attributes.title', $titleValidation);

        $summaryValidation = new StringLength([
            'min' => 1
        ]);
        $summaryValidation->setMessage(
            _('Summary can\'t be empty'),
            StringLength::TOO_SHORT
        );
        $this->validator->addValidator('data.attributes.summary', $summaryValidation);

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
