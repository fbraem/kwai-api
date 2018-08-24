<?php

namespace REST\Categories;

class CategoryEmptyValidator extends \Core\Validators\EmptyValidator
{
    public function __construct()
    {
        parent::__construct();

        $this->addValidator('data.attributes.name');
    }
}
