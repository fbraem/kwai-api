<?php

namespace REST\Categories;

use Zend\Validator\StringLength;

class CategoryValidator implements \Core\ValidatorInterface
{
    private $validator;

    public function __construct()
    {
        $this->validator = new \Core\Validator();
    }

    public function validate($data)
    {
        $nameValidation = new StringLength([
            'min' => 1,
            'max' => 255
        ]);
        $nameValidation->setMessages([
            StringLength::TOO_LONG => _('Name can\'t contain more then 255 characters'),
            StringLength::TOO_SHORT => _('Name can\'t be empty')
        ]);
        $this->validator->addValidator('data.attributes.name', $nameValidation);

        return $this->validator->validate($data);
    }
}
