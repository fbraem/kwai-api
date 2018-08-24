<?php

namespace REST\Categories;

use Zend\Validator\StringLength;

class CategoryValidator extends \Core\Validators\InputValidator
{
    public function __construct()
    {
        parent::__construct();

        $nameValidation = new StringLength([
            'min' => 1,
            'max' => 255
        ]);
        $nameValidation->setMessages([
            StringLength::TOO_LONG => _('Name can\'t contain more then 255 characters'),
            StringLength::TOO_SHORT => _('Name can\'t be empty')
        ]);
        $this->addValidator('data.attributes.name', $nameValidation);
    }
}
