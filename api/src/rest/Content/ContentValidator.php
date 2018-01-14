<?php

namespace REST\News;

use Zend\Validator\StringLength;

class ContentValidator implements \Core\ValidatorInterface
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

        return $this->validator->validate($data);
    }
}
