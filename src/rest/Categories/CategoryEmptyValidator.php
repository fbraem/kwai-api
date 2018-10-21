<?php

namespace REST\Categories;

class CategoryEmptyValidator extends \Core\Validators\EmptyValidator
{
    public function __construct()
    {
        parent::__construct();

        $this->addEmptyValidator('data.attributes.name');
    }
}
