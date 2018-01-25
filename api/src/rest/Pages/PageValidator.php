<?php

namespace REST\Pages;

class PageValidator implements \Core\ValidatorInterface
{
    private $validator;

    public function __construct()
    {
        $this->validator = new \Core\Validator();
    }

    public function validate($data)
    {
        return $this->validator->validate($data);
    }
}
