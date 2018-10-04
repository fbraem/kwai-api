<?php

namespace REST\Contents;

use Zend\Validator\StringLength;

class ContentValidator extends \Core\Validators\InputValidator
{
    public function __construct()
    {
        $titleValidation = new StringLength([
            'min' => 1,
            'max' => 255
        ]);
        $titleValidation->setMessages([
            StringLength::TOO_LONG => _('Title can\'t contain more then 255 characters'),
            StringLength::TOO_SHORT => _('Title can\'t be empty')
        ]);
        $this->addValidator('data.attributes.title', $titleValidation);

        $summaryValidation = new StringLength([
            'min' => 1
        ]);
        $summaryValidation->setMessage(
            _('Summary can\'t be empty'),
            StringLength::TOO_SHORT
        );
        $this->addValidator('data.attributes.summary', $summaryValidation);
    }
}
